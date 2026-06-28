<?php
require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/includes/icons.php';

redirect_if_logged_in($auth);

$error = '';
$resetLink = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        $error = 'Your session expired before submitting. Please try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        // false = don't try to email it (no SMTP configured locally) — we
        // show the token on screen instead, same idea as a mail-catcher.
        $result = $auth->requestReset($email, false);

        if ($result['error']) {
            $error = $result['message'];
        } else {
            $resetLink = base_url('reset-password.php') . '?token=' . urlencode($result['token']);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Reset password · <?= htmlspecialchars(APP_NAME) ?></title>
<link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
<div class="auth-shell">

  <div class="auth-brandpane">
    <div class="brand-mark">
      <span class="glyph"><?= icon('scales') ?></span>
      <span class="brand-wordmark">Legal<span>Ops</span></span>
    </div>
    <div class="pitch">
      <h1>Locked out happens. Let's fix it.</h1>
      <p>Enter the email on file and we'll generate a one-hour reset link. No mail server required on this local setup — the link appears right here.</p>
    </div>
  </div>

  <div class="auth-formpane">
    <div class="auth-card">
      <span class="eyebrow">Account recovery</span>
      <h2>Forgot your password?</h2>
      <p class="sub">We'll generate a secure, time-limited reset link.</p>

      <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <?php if ($resetLink): ?>
        <div class="alert alert-success">Reset link generated — valid for 1 hour.</div>
        <div class="reset-token-box">
          <strong>Local dev note:</strong> since no SMTP server is configured, the link is shown here instead of emailed.<br><br>
          <a href="<?= htmlspecialchars($resetLink) ?>"><?= htmlspecialchars($resetLink) ?></a>
        </div>
      <?php else: ?>
        <form method="post" novalidate>
          <?= csrf_field() ?>
          <div class="field">
            <label for="email">Account email</label>
            <input class="input" type="email" id="email" name="email" placeholder="you@yourfirm.com" required autofocus value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          </div>
          <button class="btn btn-primary btn-block" type="submit">Send reset link</button>
        </form>
      <?php endif; ?>

      <p class="auth-foot"><a href="<?= base_url('login.php') ?>">← Back to sign in</a></p>
    </div>
  </div>

</div>
</body>
</html>
