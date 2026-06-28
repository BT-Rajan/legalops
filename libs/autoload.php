<?php
/**
 * Minimal autoloader for the vendored PHPAuth library.
 * Avoids a Composer dependency so this drops straight into XAMPP.
 */

spl_autoload_register(function ($class) {
    $prefix = 'PHPAuth\\';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $path = __DIR__ . '/PHPAuth/' . str_replace('\\', '/', $relative) . '.php';

    if (is_file($path)) {
        require $path;
    }
});
