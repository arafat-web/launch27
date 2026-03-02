<?php

/**
 * Route configuration.
 *
 * Format:  'METHOD /path' => ['ControllerClass', 'method']
 *
 * Paths are matched against the URI after stripping the query string.
 * Leading slashes are normalised by the Router.
 */

return [
    'GET /' => ['HomeController', 'index'],
    'GET /booking' => ['BookingController', 'index'],
    'GET /booking/confirmation' => ['BookingController', 'confirmation'],

    // JSON / proxy endpoints
    'GET /api/services' => ['ApiController', 'services'],
    'GET /api/windows' => ['ApiController', 'windows'],
    'GET /api/proxy' => ['ApiController', 'proxy'],
    'POST /api/proxy' => ['ApiController', 'proxy'],
    'POST /api/book' => ['ApiController', 'book'],
    'GET /api/logs' => ['ApiController', 'logs'],

    // ── Admin panel ────────────────────────────────────────────────────────────
    'GET /admin' => ['AdminController', 'dashboard'],
    'GET /admin/login' => ['AdminController', 'login'],
    'POST /admin/login' => ['AdminController', 'doLogin'],
    'GET /admin/logout' => ['AdminController', 'logout'],
    'GET /admin/seo' => ['AdminController', 'seo'],
    'POST /admin/seo' => ['AdminController', 'saveSeo'],
    'GET /admin/content' => ['AdminController', 'content'],
    'POST /admin/content' => ['AdminController', 'saveContent'],
    'GET /admin/settings' => ['AdminController', 'settings'],
    'POST /admin/settings' => ['AdminController', 'saveSettings'],
    'GET /admin/visitors' => ['AdminController', 'visitors'],
    'GET /admin/bookings' => ['AdminController', 'bookings'],
    'GET /admin/bookings/export' => ['AdminController', 'exportBookings'],
    'POST /admin/bookings/status' => ['AdminController', 'updateBookingStatus'],
    'GET /admin/bookings/{id}' => ['AdminController', 'bookingDetail'],
    'GET /admin/logs' => ['AdminController', 'apiLogs'],
];
