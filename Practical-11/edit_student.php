<?php
/**
 * Practical 11: Edit Student Record
 */

session_start();
require_once __DIR__ . '/db.php';

$pdo = Database::getConnection();
$id = (int)($_GET['id'] ?? 0);
$errors = [];

// Fetch Existing Record
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$student = $stmt->fetch();

if (!$student) {
    $_SESSION['error'] = 'Requested student record not found.';
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName   = trim($_POST['fullName'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $semester   = (int)($_POST['semester'] ?? 1);
    $gpa        = (float)($_POST['gpa'] ?? 0.0);
    $city       = trim($_POST['city'] ?? '');
    $status     = trim($_POST['status'] ?? 'active');

    if (empty($fullName)) $errors[] = 'Full Name cannot be empty.';
    if (!preg_match('/^[6-9]\d{9}$/', $phone)) $errors[] = 'Valid 10-digit mobile number required.';
    if ($gpa < 0 || $gpa > 10) $errors[] = 'GPA must be between 0.00 and 10.00.';

    if (empty($errors)) {
        try {
            $upd = $pdo->prepare("
                UPDATE students 
                SET full_name = :name, phone = :phone, department = :dept, 
                    semester = :sem, gpa = :gpa, city = :city, status = :status
                WHERE id = :id
            ");
            $upd->execute([
                ':name'   => $fullName,
                ':phone'  => $phone,
                ':dept'   => $department,
                ':sem'    => $semester,
                ':gpa'    => $gpa,
                ':city'   => $city,
                ':status' => $status,
                ':id'     => $id
            ]);

            $_SESSION['success'] = "Student '{$student['roll_number']}' details updated successfully!";
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Database update error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - Practical 11</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container" style="max-width: 650px;">
        <div class="card">
            <h1>Edit Student Record</h1>
            <p style="color:var(--text-secondary); margin-bottom: 20px;">Updating profile for <strong><?= htmlspecialchars($student['roll_number']) ?></strong> (<?= htmlspecialchars($student['email']) ?>)</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="edit_student.php?id=<?= $id ?>" method="POST">
                <div class="form-group">
                    <label>Roll Number &amp; Email (Immutable)</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($student['roll_number']) ?> — <?= htmlspecialchars($student['email']) ?>" readonly style="background:#e2e8f0; cursor:not-allowed;">
                </div>

                <div class="form-group">
                    <label for="fullName">Full Legal Name *</label>
                    <input type="text" id="fullName" name="fullName" class="form-control" value="<?= htmlspecialchars($student['full_name']) ?>" required>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;" class="form-group">
                    <div>
                        <label for="phone">Mobile Number *</label>
                        <input type="tel" id="phone" name="phone" class="form-control" value="<?= htmlspecialchars($student['phone']) ?>" maxlength="10" required>
                    </div>
                    <div>
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" class="form-control" value="<?= htmlspecialchars($student['city']) ?>" required>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 2fr 1fr; gap:15px;" class="form-group">
                    <div>
                        <label for="department">Department</label>
                        <select id="department" name="department" class="form-control" required>
                            <?php 
                            $depts = ['Information Technology', 'Computer Engineering', 'Computer Science & Engineering', 'Electronics & Communication'];
                            foreach ($depts as $d): ?>
                                <option value="<?= $d ?>" <?= $student['department'] === $d ? 'selected' : '' ?>><?= $d ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="semester">Semester</label>
                        <select id="semester" name="semester" class="form-control">
                            <?php for ($s = 1; $s <= 8; $s++): ?>
                                <option value="<?= $s ?>" <?= (int)$student['semester'] === $s ? 'selected' : '' ?>>Sem <?= $s ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;" class="form-group">
                    <div>
                        <label for="gpa">Academic GPA</label>
                        <input type="number" id="gpa" name="gpa" class="form-control" step="0.01" min="0" max="10" value="<?= htmlspecialchars($student['gpa']) ?>" required>
                    </div>
                    <div>
                        <label for="status">Account Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="active" <?= $student['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $student['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            <option value="deleted" <?= $student['status'] === 'deleted' ? 'selected' : '' ?>>Deleted (Soft-Deleted)</option>
                        </select>
                    </div>
                </div>

                <div style="display:flex; justify-content:space-between; margin-top:20px;">
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Changes</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
