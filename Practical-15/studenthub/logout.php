<?php
/**
 * Practical 15: Safe Logout Controller
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/db.php';

if (!empty($_SESSION['user_id'])) {
    try {
        $pdo = Database::getConnection();
        $pdo->prepare("DELETE FROM auth_tokens WHERE user_id = :uid")->execute([':uid' => $_SESSION['user_id']]);
    } catch (Exception $e) {}
}

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
}

if (isset($_COOKIE['studenthub_remember'])) {
    setcookie('studenthub_remember', '', time() - 3600, '/', '', false, true);
}

session_destroy();
header('Location: login.php?msg=logout');
exit;
