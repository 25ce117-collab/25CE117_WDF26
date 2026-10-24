<?php
/**
 * Practical 15: Global Application Configuration
 * StudentHub - CHARUSAT Web Development Frameworks (ITUE203)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => false, // set true on production HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Database Credentials
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'studenthub_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_PORT', '3306');
define('DB_CHARSET', 'utf8mb4');

// App Metadata
define('APP_NAME', 'StudentHub');
define('APP_INSTITUTION', 'Faculty of Technology & Engineering, CHARUSAT');
define('APP_TERM', 'Odd Semester 2026-27');
define('SESSION_TIMEOUT', 900); // 15 minutes
