<?php
$pageTitle = 'Login';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/db.php';

// Redirect if already logged in
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

if ($msg === 'timeout') $error = 'Session expired due to 15 minutes of inactivity.';
elseif ($msg === 'unauthorized') $error = 'Access restricted: Insufficient administrative privileges.';
elseif ($msg === 'login_required') $error = 'Please authenticate to access your dashboard.';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password   = $_POST['password'] ?? '';
    $remember   = !empty($_POST['remember']);

    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :id1 OR username = :id2 LIMIT 1");
    $stmt->execute([':id1' => $identifier, ':id2' => $identifier]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        if ($user['status'] !== 'active') {
            $error = 'Account is deactivated. Contact administration.';
        } else {
            session_regenerate_id(true);
            $_SESSION['user_id']       = $user['id'];
            $_SESSION['username']      = $user['username'];
            $_SESSION['email']         = $user['email'];
            $_SESSION['role']          = $user['role'];
            $_SESSION['last_activity'] = time();

            // Update last_login
            $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = :id")->execute([':id' => $user['id']]);

            // Remember-Me
            if ($remember) {
                $rawToken = bin2hex(random_bytes(32));
                $tokenHash = hash('sha256', $rawToken);
                $pdo->prepare("DELETE FROM auth_tokens WHERE user_id = :uid")->execute([':uid' => $user['id']]);
                $pdo->prepare("INSERT INTO auth_tokens (user_id, token_hash, expires_at) VALUES (:uid, :hash, DATE_ADD(NOW(), INTERVAL 30 DAY))")
                    ->execute([':uid' => $user['id'], ':hash' => $tokenHash]);
                setcookie('studenthub_remember', $rawToken, time() + (30 * 24 * 3600), '/', '', false, true);
            }

            if ($user['role'] === 'admin') {
                header('Location: admin_dashboard.php');
            } else {
                header('Location: student_dashboard.php');
            }
            exit;
        }
    } else {
        $error = 'Invalid email/username or password.';
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div style="max-width: 440px; margin: 30px auto;" class="card">
    <h1 style="text-align: center; margin-bottom: 5px;">Portal Sign In</h1>
    <p style="text-align: center; color: var(--text-secondary); margin-bottom: 20px;">Access your StudentHub dashboard</p>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="identifier">Email Address or Username</label>
            <input type="text" id="identifier" name="identifier" class="form-control" placeholder="admin@charusat.edu.in or 23it042@charusat.edu.in" required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <div class="form-group" style="display: flex; justify-content: space-between; align-items: center;">
            <label style="font-weight: normal; cursor: pointer; display: flex; align-items: center; gap: 6px; margin-bottom: 0;">
                <input type="checkbox" name="remember" value="1">
                <span>Remember me</span>
            </label>
        </div>

        <button type="submit" class="btn btn-primary btn-block" style="padding: 11px;">Sign In</button>
    </form>

    <div style="margin-top: 20px; padding-top: 15px; border-top: 1px dashed var(--border); font-size: 0.85rem; color: var(--text-secondary);">
        <strong>Demo Credentials:</strong><br>
        Admin: <code>admin@charusat.edu.in</code> | <code>Admin@123</code><br>
        Student: <code>23it042@charusat.edu.in</code> | <code>Student@123</code>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
