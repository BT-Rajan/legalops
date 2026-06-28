<?php
require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/includes/icons.php';

redirect_if_logged_in($auth);

$token = trim($_GET['token'] ?? $_POST['token'] ?? '');
$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        $error = 'Your session expired before submitting. Please try again.';
    } else {
        $password = (string)($_POST['password'] ?? '');
        $repeat = (string)($_POST['password_confirm'] ?? '');
        $result = $auth->resetPass($token, $password, $repeat);

        if ($result['error']) {
            $error = $result['message'];
        } else {
            $success = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Set new password · <?= htmlspecialchars(APP_NAME) ?></title>
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
      <h1>Almost there.</h1>
      <p>Choose a new password to finish recovering your account. This link can only be used once.</p>
    </div>
  </div>

  <div class="auth-formpane">
    <div class="auth-card">
      <span class="eyebrow">Account recovery</span>
      <h2>Set a new password</h2>
      <p class="sub">Make it something you haven't used here before.</p>

      <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="alert alert-success">Password updated. You can sign in now.</div>
        <a class="btn btn-primary btn-block" href="<?= base_url('login.php') ?>">Go to sign in</a>
      <?php elseif (!$token): ?>
        <div class="alert alert-error">This reset link is missing its token. Request a new one.</div>
        <a class="btn btn-ghost btn-block" href="<?= base_url('forgot-password.php') ?>">Request a new link</a>
      <?php else: ?>
        <form method="post" novalidate>
          <?= csrf_field() ?>
          <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
          <div class="field">
            <label for="password">New password</label>
            <input class="input" type="password" id="password" name="password" placeholder="8+ characters" required minlength="8" autofocus>
          </div>
          <div class="field">
            <label for="password_confirm">Confirm new password</label>
            <input class="input" type="password" id="password_confirm" name="password_confirm" placeholder="Repeat password" required minlength="8">
          </div>
          <button class="btn btn-primary btn-block" type="submit">Update password</button>
        </form>
      <?php endif; ?>

      <p class="auth-foot"><a href="<?= base_url('login.php') ?>">← Back to sign in</a></p>
    </div>
  </div>

</div>
</body>
</html>
