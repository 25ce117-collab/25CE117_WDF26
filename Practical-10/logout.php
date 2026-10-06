<?php
/**
 * Practical 10: Secure Logout Controller
 * Destroys session, deletes database remember-me tokens, and clears client cookies
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

// 1. Purge database remember-me tokens if user was logged in
if (!empty($_SESSION['user_id'])) {
    try {
        $pdo = Database::getConnection();
        $delStmt = $pdo->prepare("DELETE FROM auth_tokens WHERE user_id = :uid");
        $delStmt->execute([':uid' => $_SESSION['user_id']]);
    } catch (Exception $e) {
        // continue logout anyway
    }
}

// 2. Clear Session Superglobal
$_SESSION = [];

// 3. Clear Session Cookie from browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 4. Clear Remember-Me persistent cookie
if (isset($_COOKIE['studenthub_remember'])) {
    setcookie('studenthub_remember', '', time() - 3600, '/', '', false, true);
}

// 5. Destroy Session
session_destroy();

header('Location: login.php?msg=logout');
exit;
