<?php
require_once __DIR__ . '/config/bootstrap.php';

header('Location: ' . base_url($auth->isLogged() ? 'dashboard.php' : 'login.php'));
exit;
