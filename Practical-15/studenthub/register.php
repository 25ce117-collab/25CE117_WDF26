<?php
$pageTitle = 'Register Account';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth_guard.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Security verification failed (Invalid CSRF token).';
    } else {
        $fullName   = trim(filter_input(INPUT_POST, 'fullName', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $username   = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $email      = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '');
        $phone      = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $rollNumber = trim(filter_input(INPUT_POST, 'rollNumber', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $department = trim(filter_input(INPUT_POST, 'department', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $password   = $_POST['password'] ?? '';
        $confirmPass = $_POST['confirmPassword'] ?? '';

        if (!preg_match('/^[A-Za-z\s]{3,50}$/', $fullName)) $errors[] = 'Name must be 3-50 letters only.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid institutional email is required.';
        if (!preg_match('/^[6-9]\d{9}$/', $phone)) $errors[] = 'Phone must be a valid 10-digit number.';
        if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password requires at least 8 characters with 1 uppercase letter and 1 digit.';
        }
        if ($password !== $confirmPass) $errors[] = 'Passwords do not match.';

        if (empty($errors)) {
            $pdo = Database::getConnection();
            $check = $pdo->prepare("SELECT id FROM users WHERE email = :email OR username = :uname LIMIT 1");
            $check->execute([':email' => $email, ':uname' => $username]);
            if ($check->fetch()) {
                $errors[] = 'Email or username already registered.';
            } else {
                try {
                    $pdo->beginTransaction();
                    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

                    $uStmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role, status) VALUES (:uname, :email, :hash, 'student', 'active')");
                    $uStmt->execute([':uname' => $username, ':email' => $email, ':hash' => $hash]);
                    $userId = $pdo->lastInsertId();

                    $sStmt = $pdo->prepare("INSERT INTO students (user_id, roll_number, full_name, email, phone, department, semester, status) VALUES (:uid, :roll, :name, :email, :phone, :dept, 3, 'active')");
                    $sStmt->execute([':uid' => $userId, ':roll' => $rollNumber, ':name' => $fullName, ':email' => $email, ':phone' => $phone, ':dept' => $department]);

                    $aStmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (:uid, 'STUDENT_REGISTERED', :dt, :ip)");
                    $aStmt->execute([':uid' => $userId, ':dt' => "New student registered: {$fullName} ({$rollNumber})", ':ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1']);

                    $pdo->commit();
                    $success = "Registration successful! You may now sign in.";
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $errors[] = 'Database insertion error: ' . $e->getMessage();
                }
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div style="max-width: 650px; margin: 30px auto;" class="card">
    <h1>Create Student Account</h1>
    <p style="color:var(--text-secondary); margin-bottom: 20px;">Join StudentHub with your university credentials.</p>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">✓ <?= htmlspecialchars($success) ?> <a href="login.php" style="font-weight:bold; margin-left:10px;">Login Now &rarr;</a></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken()) ?>">

        <div class="form-group">
            <label for="fullName">Full Legal Name *</label>
            <input type="text" id="fullName" name="fullName" class="form-control" required placeholder="Satyajit Parmar">
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;" class="form-group">
            <div>
                <label for="username">Username *</label>
                <input type="text" id="username" name="username" class="form-control" required placeholder="satya_p">
            </div>
            <div>
                <label for="rollNumber">Roll Number *</label>
                <input type="text" id="rollNumber" name="rollNumber" class="form-control" required placeholder="23IT042">
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;" class="form-group">
            <div>
                <label for="email">Institutional Email *</label>
                <input type="email" id="email" name="email" class="form-control" required placeholder="user@charusat.edu.in">
            </div>
            <div>
                <label for="phone">Mobile Number *</label>
                <input type="tel" id="phone" name="phone" class="form-control" required placeholder="9876543210" maxlength="10">
            </div>
        </div>

        <div class="form-group">
            <label for="department">Department *</label>
            <select id="department" name="department" class="form-control" required>
                <option value="Information Technology">Information Technology (CSPIT)</option>
                <option value="Computer Engineering">Computer Engineering (CSPIT)</option>
                <option value="Computer Science & Engineering">Computer Science &amp; Eng (DEPSTAR)</option>
            </select>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;" class="form-group">
            <div>
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="Min 8 chars, 1 upper, 1 num">
            </div>
            <div>
                <label for="confirmPassword">Confirm Password *</label>
                <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" required placeholder="Repeat password">
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block" style="padding:12px; margin-top:15px;">Complete Registration</button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
