<?php
/**
 * Practical 15: Central Authentication & Route Guard Middleware
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

function checkAuth(?string $requiredRole = null): array {
    $now = time();

    // 1. Check Active Session
    if (!empty($_SESSION['user_id'])) {
        if (isset($_SESSION['last_activity']) && ($now - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
            session_unset();
            session_destroy();
            header('Location: login.php?msg=timeout');
            exit;
        }
        $_SESSION['last_activity'] = $now;

        if ($requiredRole !== null && ($_SESSION['role'] ?? '') !== $requiredRole) {
            header('Location: login.php?msg=unauthorized');
            exit;
        }

        return [
            'id'       => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'email'    => $_SESSION['email'],
            'role'     => $_SESSION['role']
        ];
    }

    // 2. Check Remember-Me Cookie Auto-Login
    if (!empty($_COOKIE['studenthub_remember'])) {
        $raw = $_COOKIE['studenthub_remember'];
        $hash = hash('sha256', $raw);

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT u.id, u.username, u.email, u.role, u.status 
            FROM auth_tokens t
            INNER JOIN users u ON t.user_id = u.id
            WHERE t.token_hash = :hash AND t.expires_at > NOW() AND u.status = 'active'
            LIMIT 1
        ");
        $stmt->execute([':hash' => $hash]);
        $user = $stmt->fetch();

        if ($user) {
            session_regenerate_id(true);
            $_SESSION['user_id']       = $user['id'];
            $_SESSION['username']      = $user['username'];
            $_SESSION['email']         = $user['email'];
            $_SESSION['role']          = $user['role'];
            $_SESSION['last_activity'] = $now;

            if ($requiredRole !== null && $user['role'] !== $requiredRole) {
                header('Location: login.php?msg=unauthorized');
                exit;
            }

            return $user;
        }
    }

    // Not authenticated
    header('Location: login.php?msg=login_required');
    exit;
}

function getCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(?string $token): bool {
    return !empty($_SESSION['csrf_token']) && !empty($token) && hash_equals($_SESSION['csrf_token'], $token);
}
