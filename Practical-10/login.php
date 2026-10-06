<?php
/**
 * Practical 10: Secure Login Controller with Sessions & Remember-Me
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

// If already logged in, redirect based on role
if (!empty($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: student_dashboard.php');
    }
    exit;
}

$error = '';
$msg = $_GET['msg'] ?? '';

if ($msg === 'timeout') {
    $error = 'Your session expired due to 15 minutes of inactivity. Please log in again.';
} elseif ($msg === 'logout') {
    $success = 'You have been safely logged out.';
} elseif ($msg === 'unauthorized') {
    $error = 'Access restricted: You do not possess the required administrative permissions.';
} elseif ($msg === 'login_required') {
    $error = 'Authentication required. Please sign in to access your portal dashboard.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginIdentifier = trim($_POST['identifier'] ?? '');
    $password        = $_POST['password'] ?? '';
    $remember        = !empty($_POST['remember']);

    if (empty($loginIdentifier) || empty($password)) {
        $error = 'Please enter both your email/username and password.';
    } else {
        $pdo = Database::getConnection();

        // 1. Fetch User Record
        $stmt = $pdo->prepare("
            SELECT id, username, email, password_hash, role, status 
            FROM users 
            WHERE email = :id1 OR username = :id2 
            LIMIT 1
        ");
        $stmt->execute([':id1' => $loginIdentifier, ':id2' => $loginIdentifier]);
        $user = $stmt->fetch();

        // 2. Verify Credentials
        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['status'] !== 'active') {
                $error = 'Your account has been deactivated. Please contact the administrator.';
            } else {
                // 3. Prevent Session Fixation via session_regenerate_id(true)
                session_regenerate_id(true);

                $_SESSION['user_id']       = $user['id'];
                $_SESSION['username']      = $user['username'];
                $_SESSION['email']         = $user['email'];
                $_SESSION['role']          = $user['role'];
                $_SESSION['last_activity'] = time();

                // 4. Update Last-Login Timestamp (Intermediate Extension)
                $updStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
                $updStmt->execute([':id' => $user['id']]);

                // 5. Secure Remember-Me Token via SHA-256 Hash in DB (Advanced Extension)
                if ($remember) {
                    $rawToken = bin2hex(random_bytes(32)); // 64-char cryptographically secure token
                    $tokenHash = hash('sha256', $rawToken);
                    $expiresAt = date('Y-m-d H:i:s', time() + (30 * 24 * 3600)); // 30 days

                    // Delete old tokens for user
                    $delStmt = $pdo->prepare("DELETE FROM auth_tokens WHERE user_id = :uid");
                    $delStmt->execute([':uid' => $user['id']]);

                    // Insert new token hash
                    $tokStmt = $pdo->prepare("
                        INSERT INTO auth_tokens (user_id, token_hash, expires_at) 
                        VALUES (:uid, :hash, :exp)
                    ");
                    $tokStmt->execute([
                        ':uid'  => $user['id'],
                        ':hash' => $tokenHash,
                        ':exp'  => $expiresAt
                    ]);

                    // Set HttpOnly cookie
                    setcookie('studenthub_remember', $rawToken, time() + (30 * 24 * 3600), '/', '', false, true);
                }

                // 6. Role-Based Redirection
                if ($user['role'] === 'admin') {
                    header('Location: admin_dashboard.php');
                } else {
                    header('Location: student_dashboard.php');
                }
                exit;
            }
        } else {
            $error = 'Invalid credentials. Please verify your email/username and password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Secure Login (Practical 10)</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="card">
            <h1>StudentHub Portal Login</h1>
            <p class="subtitle">Practical 10: Session Security, RBAC, Inactivity Timeout &amp; Remember-Me</p>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">✓ <?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="identifier">Email or Username</label>
                    <input type="text" id="identifier" name="identifier" class="form-control" 
                           placeholder="admin@charusat.edu.in or 23it042@charusat.edu.in" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" 
                           placeholder="Enter password" required>
                </div>

                <div class="form-group" style="display: flex; justify-content: space-between; align-items: center;">
                    <label style="font-weight: normal; cursor: pointer; display: flex; align-items: center; gap: 6px; margin-bottom: 0;">
                        <input type="checkbox" name="remember" value="1">
                        <span>Remember Me (30 Days)</span>
                    </label>
                </div>

                <button type="submit" class="btn" style="margin-top: 10px;">Sign In</button>
            </form>

            <div style="margin-top: 25px; padding-top: 15px; border-top: 1px dashed var(--border); font-size: 0.85rem; color: var(--text-secondary);">
                <strong>Demo Test Credentials:</strong><br>
                👑 <strong>Admin:</strong> <code>admin@charusat.edu.in</code> | <code>Admin@123</code><br>
                🎓 <strong>Student:</strong> <code>23it042@charusat.edu.in</code> | <code>Student@123</code>
            </div>
        </div>
    </div>
</body>
</html>
