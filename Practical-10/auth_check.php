<?php
/**
 * Practical 10: Session Security & Role-Based Access Guard Middleware
 * Enforces:
 * 1. Active authentication check
 * 2. 15-minute inactivity session timeout
 * 3. Remember-Me persistent cookie auto-authentication
 * 4. Role-based view authorization
 */

if (session_status() === PHP_SESSION_NONE) {
    // Configure secure session cookie parameters
    session_set_cookie_params([
        'lifetime' => 0, // session cookie expires on browser close
        'path'     => '/',
        'secure'   => false, // set true in production HTTPS
        'httponly' => true,  // prevent JavaScript access (neutralize XSS cookie theft)
        'samesite' => 'Lax'
    ]);
    session_start();
}

require_once __DIR__ . '/db.php';

const SESSION_INACTIVITY_TIMEOUT = 900; // 15 minutes = 900 seconds

/**
 * Validates session activity or attempts Remember-Me token auto-login
 */
function checkAuth(?string $requiredRole = null): array {
    $now = time();

    // 1. Check for Active Session
    if (!empty($_SESSION['user_id'])) {
        // Enforce Inactivity Timeout
        if (isset($_SESSION['last_activity']) && ($now - $_SESSION['last_activity']) > SESSION_INACTIVITY_TIMEOUT) {
            // Session expired due to inactivity
            session_unset();
            session_destroy();
            header('Location: login.php?msg=timeout');
            exit;
        }
        $_SESSION['last_activity'] = $now;

        // Role verification
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

    // 2. Attempt Auto-Login via Secure Remember-Me Cookie (Advanced Extension)
    if (!empty($_COOKIE['studenthub_remember'])) {
        $rawToken = $_COOKIE['studenthub_remember'];
        $tokenHash = hash('sha256', $rawToken);

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT u.id, u.username, u.email, u.role, u.status 
            FROM auth_tokens t
            INNER JOIN users u ON t.user_id = u.id
            WHERE t.token_hash = :hash AND t.expires_at > NOW() AND u.status = 'active'
            LIMIT 1
        ");
        $stmt->execute([':hash' => $tokenHash]);
        $user = $stmt->fetch();

        if ($user) {
            // Regenerate session ID upon token login to prevent fixation
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
        } else {
            // Expired or invalid cookie; clear it
            setcookie('studenthub_remember', '', time() - 3600, '/', '', false, true);
        }
    }

    // Not authenticated; redirect to login
    header('Location: login.php?msg=login_required');
    exit;
}
