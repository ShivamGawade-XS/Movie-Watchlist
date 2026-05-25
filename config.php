<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Database configuration - update these values for your environment
if (!defined('DB_HOST')) {
    define('DB_HOST', '127.0.0.1');
    define('DB_NAME', 'movie_watchlist');
    define('DB_USER', 'root');
    define('DB_PASS', 'password');
}

define('APP_NAME', 'CineLog');

define('BASE_URL', '');

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';
