<?php

// ── Launch27 API Credentials ──────────────────────────────────────────────────
define('L27_API_KEY',    getenv('L27_API_KEY')    ?: 'live_67gMRqqzGi5I4oim3wAC');
define('L27_COMPANY_ID', getenv('L27_COMPANY_ID') ?: 'arafatweb');
define('L27_SUBDOMAIN',  getenv('L27_SUBDOMAIN')  ?: 'arafatweb');
define('L27_BASE_URL',   'https://' . L27_SUBDOMAIN . '.launch27.com/v1');

// ── Paths ──────────────────────────────────────────────────────────────────────
define('ROOT_DIR',  dirname(__DIR__));
define('LOG_DIR',   ROOT_DIR . '/logs');
define('VIEW_DIR',  ROOT_DIR . '/app/Views');
define('CORE_DIR',  ROOT_DIR . '/core');
define('APP_DIR',   ROOT_DIR . '/app');

// ── Ensure log directory exists ────────────────────────────────────────────────
if (!is_dir(LOG_DIR)) {
    mkdir(LOG_DIR, 0755, true);
}

// ── Error display (off in production) ─────────────────────────────────────────
error_reporting(E_ALL);
ini_set('display_errors', 0);
