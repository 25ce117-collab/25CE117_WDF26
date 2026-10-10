<?php
/**
 * Practical 11: Add Student Handler
 */

session_start();
require_once __DIR__ . '/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rollNumber = trim($_POST['rollNumber'] ?? '');
    $fullName   = trim($_POST['fullName'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $semester   = (int)($_POST['semester'] ?? 1);
    $gpa        = (float)($_POST['gpa'] ?? 0.0);
    $city       = trim($_POST['city'] ?? 'Anand');

    if (empty($rollNumber)) $errors[] = 'Roll Number is required.';
    if (empty($fullName)) $errors[] = 'Full Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
    if (!preg_match('/^[6-9]\d{9}$/', $phone)) $errors[] = '10-digit Indian mobile number is required.';
    if (empty($department)) $errors[] = 'Department selection is mandatory.';
    if ($gpa < 0 || $gpa > 10) $errors[] = 'GPA must be between 0.00 and 10.00.';

    if (empty($errors)) {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                INSERT INTO students (roll_number, full_name, email, phone, department, semester, gpa, city, status)
                VALUES (:roll, :name, :email, :phone, :dept, :sem, :gpa, :city, 'active')
            ");
            $stmt->execute([
                ':roll'  => $rollNumber,
                ':name'  => $fullName,
                ':email' => $email,
                ':phone' => $phone,
                ':dept'  => $department,
                ':sem'   => $semester,
                ':gpa'   => $gpa,
                ':city'  => $city
            ]);

            $_SESSION['success'] = "Student '{$fullName}' ({$rollNumber}) created successfully!";
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errors[] = 'A student with this Roll Number or Email already exists in the directory.';
            } else {
                $errors[] = 'Database error: ' . $e->getMessage();
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
    <title>Add Student - Practical 11</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container" style="max-width: 650px;">
        <div class="card">
            <h1>Add New Student Record</h1>
            <p style="color:var(--text-secondary); margin-bottom: 20px;">Insert a verified student profile into the central directory.</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="add_student.php" method="POST">
                <div style="display:grid; grid-template-columns: 1fr 2fr; gap:15px;" class="form-group">
                    <div>
                        <label for="rollNumber">Roll Number *</label>
                        <input type="text" id="rollNumber" name="rollNumber" class="form-control" placeholder="23IT042" required>
                    </div>
                    <div>
                        <label for="fullName">Full Legal Name *</label>
                        <input type="text" id="fullName" name="fullName" class="form-control" placeholder="Satyajit Parmar" required>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;" class="form-group">
                    <div>
                        <label for="email">Institutional Email *</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="user@charusat.edu.in" required>
                    </div>
                    <div>
                        <label for="phone">Mobile Number *</label>
                        <input type="tel" id="phone" name="phone" class="form-control" placeholder="9876543210" maxlength="10" required>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 2fr 1fr; gap:15px;" class="form-group">
                    <div>
                        <label for="department">Department *</label>
                        <select id="department" name="department" class="form-control" required>
                            <option value="Information Technology">Information Technology (CSPIT)</option>
                            <option value="Computer Engineering">Computer Engineering (CSPIT)</option>
                            <option value="Computer Science & Engineering">Computer Science &amp; Eng (DEPSTAR)</option>
                            <option value="Electronics & Communication">Electronics &amp; Comm</option>
                        </select>
                    </div>
                    <div>
                        <label for="semester">Semester</label>
                        <select id="semester" name="semester" class="form-control">
                            <?php for ($s = 1; $s <= 8; $s++): ?>
                                <option value="<?= $s ?>" <?= $s === 3 ? 'selected' : '' ?>>Sem <?= $s ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;" class="form-group">
                    <div>
                        <label for="gpa">Current CGPA / GPA (0.0 - 10.0)</label>
                        <input type="number" id="gpa" name="gpa" class="form-control" step="0.01" min="0" max="10" value="9.00" required>
                    </div>
                    <div>
                        <label for="city">Residential City</label>
                        <input type="text" id="city" name="city" class="form-control" value="Anand" required>
                    </div>
                </div>

                <div style="display:flex; justify-content:space-between; margin-top:20px;">
                    <a href="index.php" class="btn btn-secondary">← Back to Directory</a>
                    <button type="submit" class="btn btn-primary">Save Student Record</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
