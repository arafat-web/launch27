<?php
/**
 * Front Controller
 * ─────────────────
 * Entry point for every HTTP request. Loads config, autoloads
 * controller/core classes, then dispatches via the Router.
 */

// ── Bootstrap ──────────────────────────────────────────────────────────────────
require_once __DIR__ . '/config/config.php';

// ── Simple PSR-4-style autoloader ─────────────────────────────────────────────
spl_autoload_register(function (string $class): void {
    $map = [
        'Router' => CORE_DIR . '/Router.php',
        'View' => CORE_DIR . '/View.php',
        'Api' => CORE_DIR . '/Api.php',
        'Logger' => CORE_DIR . '/Logger.php',
        'Database' => CORE_DIR . '/Database.php',
        'Auth' => CORE_DIR . '/Auth.php',
        'VisitorTracker' => CORE_DIR . '/VisitorTracker.php',
        'HomeController' => APP_DIR . '/Controllers/HomeController.php',
        'BookingController' => APP_DIR . '/Controllers/BookingController.php',
        'ApiController' => APP_DIR . '/Controllers/ApiController.php',
        'AdminController' => APP_DIR . '/Controllers/AdminController.php',
    ];

    if (isset($map[$class])) {
        require_once $map[$class];
    }
});

// ── Start session (needed for Auth) ────────────────────────────────────────────
Auth::start();

// ── Load routes & dispatch ─────────────────────────────────────────────────────
$routes = require_once __DIR__ . '/config/routes.php';

$router = new Router($routes);
$router->dispatch();

