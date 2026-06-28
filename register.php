<?php
require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/includes/icons.php';

redirect_if_logged_in($auth);

$error = '';
$old = ['full_name' => '', 'job_title' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['full_name'] = trim($_POST['full_name'] ?? '');
    $old['job_title'] = trim($_POST['job_title'] ?? '');
    $old['email'] = trim($_POST['email'] ?? '');

    if (!csrf_valid()) {
        $error = 'Your session expired before submitting. Please try again.';
    } elseif ($old['full_name'] === '') {
        $error = 'Please tell us your full name.';
    } else {
        $password = (string)($_POST['password'] ?? '');
        $repeat = (string)($_POST['password_confirm'] ?? '');

        $result = $auth->register($old['email'], $password, $repeat, [
            'full_name' => $old['full_name'],
            'job_title' => $old['job_title'] !== '' ? $old['job_title'] : 'Team member',
        ]);

        if ($result['error']) {
            $error = $result['message'];
        } else {
            log_activity($pdo, (int)$result['uid'], 'account_created', 'Created an account on LegalOps');
            flash('success', 'Account created — sign in to continue.');
            header('Location: ' . base_url('login.php'));
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
<title>Create account · <?= htmlspecialchars(APP_NAME) ?></title>
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
      <h1>One workspace for every matter on your desk.</h1>
      <p>Set up your seat in under a minute. No credit card, no SMTP server, no waiting on an activation email — this runs entirely on your own machine.</p>
    </div>
    <div class="brand-stats">
      <div class="stat"><b>bcrypt</b><span>Password hashing</span></div>
      <div class="stat"><b>0</b><span>Third parties involved</span></div>
      <div class="stat"><b>1 min</b><span>To get set up</span></div>
    </div>
  </div>

  <div class="auth-formpane">
    <div class="auth-card">
      <span class="eyebrow">Get started</span>
      <h2>Create your account</h2>
      <p class="sub">You'll be the first user on this firm's LegalOps instance.</p>

      <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post" novalidate>
        <?= csrf_field() ?>
        <div class="field">
          <label for="full_name">Full name</label>
          <input class="input" type="text" id="full_name" name="full_name" placeholder="Aishwarya Krishnan" required autofocus value="<?= htmlspecialchars($old['full_name']) ?>">
        </div>
        <div class="field">
          <label for="job_title">Role at the firm</label>
          <input class="input" type="text" id="job_title" name="job_title" placeholder="Associate, Paralegal, Partner…" value="<?= htmlspecialchars($old['job_title']) ?>">
        </div>
        <div class="field">
          <label for="email">Work email</label>
          <input class="input" type="email" id="email" name="email" placeholder="you@yourfirm.com" required value="<?= htmlspecialchars($old['email']) ?>">
        </div>
        <div class="input-row">
          <div class="field">
            <label for="password">Password</label>
            <input class="input" type="password" id="password" name="password" placeholder="8+ characters" required minlength="8">
          </div>
          <div class="field">
            <label for="password_confirm">Confirm password</label>
            <input class="input" type="password" id="password_confirm" name="password_confirm" placeholder="Repeat password" required minlength="8">
          </div>
        </div>
        <button class="btn btn-primary btn-block" type="submit">Create account</button>
      </form>

      <p class="auth-foot">Already on LegalOps? <a href="<?= base_url('login.php') ?>">Sign in</a></p>
    </div>
  </div>

</div>
</body>
</html>
