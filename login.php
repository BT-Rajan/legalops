<?php
require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/includes/icons.php';

redirect_if_logged_in($auth);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        $error = 'Your session expired before submitting. Please try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');
        $remember = isset($_POST['remember']) ? 1 : 0;

        $result = $auth->login($email, $password, $remember);

        if ($result['error']) {
            $error = $result['message'];
        } else {
            if ($phpauth_config->uses_session) {
                $_SESSION[$result['cookie_name']] = $result['hash'];
                $_SESSION[$result['cookie_name'] . '_expire'] = $result['expire'];
            } else {
                setcookie(
                    $result['cookie_name'],
                    $result['hash'],
                    $result['expire'],
                    $phpauth_config->cookie_path,
                    $phpauth_config->cookie_domain,
                    (bool)$phpauth_config->cookie_secure,
                    (bool)$phpauth_config->cookie_http
                );
            }

            $uid = $auth->getUID($email);
            log_activity($pdo, $uid, 'login', 'Signed in to LegalOps');

            flash('success', 'Welcome back.');
            header('Location: ' . base_url('dashboard.php'));
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sign in · <?= htmlspecialchars(APP_NAME) ?></title>
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
      <h1>Practice management, run the way a good firm runs.</h1>
      <p>Cases, clients, deadlines and billing — one quiet, well-kept ledger for the whole practice. Built for the partners who'd rather their software stayed out of the way.</p>
    </div>

    <div class="brand-stats">
      <div class="stat"><b>6</b><span>Active matters</span></div>
      <div class="stat"><b>99.9%</b><span>Uptime, local</span></div>
      <div class="stat"><b>256-bit</b><span>Password hashing</span></div>
    </div>
  </div>

  <div class="auth-formpane">
    <div class="auth-card">
      <span class="eyebrow">Welcome back</span>
      <h2>Sign in to your firm</h2>
      <p class="sub">Enter your credentials to access the practice dashboard.</p>

      <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post" novalidate>
        <?= csrf_field() ?>
        <div class="field">
          <label for="email">Work email</label>
          <input class="input" type="email" id="email" name="email" placeholder="you@yourfirm.com" required autofocus value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="field">
          <label for="password">Password</label>
          <input class="input" type="password" id="password" name="password" placeholder="••••••••" required>
        </div>
        <div class="check-row">
          <label><input type="checkbox" name="remember" value="1"> Keep me signed in</label>
          <a href="<?= base_url('forgot-password.php') ?>">Forgot password?</a>
        </div>
        <button class="btn btn-primary btn-block" type="submit">Sign in</button>
      </form>

      <div class="reset-token-box" style="margin-top:20px;background:var(--accent-100);border-color:var(--accent-600)">
        <strong>Demo account</strong> — demo@legalops.local / LegalOps@123
      </div>

      <p class="auth-foot">New to LegalOps? <a href="<?= base_url('register.php') ?>">Create an account</a></p>
    </div>
  </div>

</div>
</body>
</html>
