<?php
require_once __DIR__ . '/config/bootstrap.php';

if ($auth->isLogged()) {
    $auth->logout($auth->getCurrentSessionHash());
}
session_destroy();

header('Location: ' . base_url('login.php'));
exit;
