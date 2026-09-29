<?php
/**
 * Practical 9: Secure User Registration with Database Insert, Duplicate Check & Bcrypt Hashing
 * Intermediate Extension: Prepared statements (PDO)
 * Advanced Extension: CSRF token validation and Audit Log insertion
 */

require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/db.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Verify CSRF Token
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Security verification failed (Invalid CSRF Token).';
    } else {
        $pdo = Database::getConnection();

        // 2. Sanitize and Extract Inputs
        $fullName   = trim(filter_input(INPUT_POST, 'fullName', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $username   = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $email      = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '');
        $phone      = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $rollNumber = trim(filter_input(INPUT_POST, 'rollNumber', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $department = trim(filter_input(INPUT_POST, 'department', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $password   = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirmPassword'] ?? '';

        // 3. Backend Validation Rules
        if (!preg_match('/^[A-Za-z\s]{3,50}$/', $fullName)) {
            $errors[] = 'Full Name must contain 3-50 alphabetic characters and spaces.';
        }
        if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
            $errors[] = 'Username must be 3-30 characters (letters, numbers, underscores).';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please provide a valid email address.';
        }
        if (!preg_match('/^[6-9]\d{9}$/', $phone)) {
            $errors[] = 'Phone number must be a 10-digit number starting with 6-9.';
        }
        if (empty($rollNumber)) {
            $errors[] = 'Student Roll Number is required.';
        }
        if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must be at least 8 characters with at least 1 uppercase letter and 1 digit.';
        }
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        }

        // 4. Duplicate Check via Prepared Statements
        if (empty($errors)) {
            $checkStmt = $pdo->prepare("SELECT email, username FROM users WHERE email = :email OR username = :username LIMIT 1");
            $checkStmt->execute([':email' => $email, ':username' => $username]);
            $existing = $checkStmt->fetch();

            if ($existing) {
                if ($existing['email'] === $email) {
                    $errors[] = 'This email address is already registered. Please sign in or use another email.';
                }
                if ($existing['username'] === $username) {
                    $errors[] = 'This username is already taken. Please choose another username.';
                }
            }
        }

        // 5. Secure Password Hashing & Insertion
        if (empty($errors)) {
            try {
                $pdo->beginTransaction();

                // Generate secure Bcrypt hash (Cost factor 12)
                $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

                // Insert into USERS table
                $userStmt = $pdo->prepare("
                    INSERT INTO users (username, email, password_hash, role, status) 
                    VALUES (:username, :email, :hash, 'student', 'active')
                ");
                $userStmt->execute([
                    ':username' => $username,
                    ':email'    => $email,
                    ':hash'     => $passwordHash
                ]);
                $userId = $pdo->lastInsertId();

                // Insert into STUDENTS table
                $studentStmt = $pdo->prepare("
                    INSERT INTO students (user_id, roll_number, full_name, email, phone, department, semester, status)
                    VALUES (:user_id, :roll, :name, :email, :phone, :dept, 3, 'active')
                ");
                $studentStmt->execute([
                    ':user_id' => $userId,
                    ':roll'    => $rollNumber,
                    ':name'    => $fullName,
                    ':email'   => $email,
                    ':phone'   => $phone,
                    ':dept'    => $department
                ]);

                // Advanced Extension: Record into AUDIT_LOGS table
                $auditStmt = $pdo->prepare("
                    INSERT INTO audit_logs (user_id, action, details, ip_address)
                    VALUES (:user_id, 'USER_REGISTERED', :details, :ip)
                ");
                $auditStmt->execute([
                    ':user_id' => $userId,
                    ':details' => "New student registered: {$username} ({$email}) with Roll No {$rollNumber}",
                    ':ip'      => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
                ]);

                $pdo->commit();
                $success = "Registration successful! Account created for {$username}. Your password has been securely hashed using Bcrypt.";
                
                // Clear CSRF token to prevent form re-submission
                unset($_SESSION['csrf_token']);

            } catch (Exception $e) {
                $pdo->rollBack();
                $errors[] = "Database insertion failed: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 9 - Secure User Registration</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="card">
        <h1>StudentHub Secure Registration</h1>
        <p class="subtitle">Practical 9: Bcrypt Password Hashing, Duplicate Checks &amp; Audit Logging</p>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <strong>✓ Success:</strong> <?= htmlspecialchars($success) ?>
                <div style="margin-top:10px;"><a href="../Practical-10/login.php" style="color:#166534; font-weight:bold;">Proceed to Login &rarr;</a></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <strong>⚠️ Registration Failed:</strong>
                <ul style="margin-left: 20px; margin-top: 6px;">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken()) ?>">

            <div class="form-group">
                <label for="fullName">Full Name *</label>
                <input type="text" id="fullName" name="fullName" class="form-control" required placeholder="Satyajit Parmar">
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label for="username">Username *</label>
                    <input type="text" id="username" name="username" class="form-control" required placeholder="satya_parmar">
                </div>
                <div class="form-group">
                    <label for="rollNumber">Roll Number *</label>
                    <input type="text" id="rollNumber" name="rollNumber" class="form-control" required placeholder="23IT042">
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label for="email">Institutional Email *</label>
                    <input type="email" id="email" name="email" class="form-control" required placeholder="user@charusat.edu.in">
                </div>
                <div class="form-group">
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

            <div class="grid-2">
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" class="form-control" required placeholder="Min 8 chars, 1 upper, 1 num">
                </div>
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password *</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" required placeholder="Repeat password">
                </div>
            </div>

            <button type="submit" class="btn" style="margin-top: 15px;">Create Secure Account</button>
        </form>
    </div>
</body>
</html>
