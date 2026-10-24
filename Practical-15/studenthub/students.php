<?php
$pageTitle = 'Student Directory CRUD';
require_once __DIR__ . '/includes/auth_guard.php';

$currentUser = checkAuth('admin');
$pdo = Database::getConnection();

// Action: Handle Soft Delete or Restore
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] === 'restore') {
        $pdo->prepare("UPDATE students SET status = 'active' WHERE id = :id")->execute([':id' => $id]);
        $_SESSION['msg'] = "Student #{$id} restored to active status!";
    } elseif ($_GET['action'] === 'delete') {
        $pdo->prepare("UPDATE students SET status = 'deleted' WHERE id = :id")->execute([':id' => $id]);
        $_SESSION['msg'] = "Student #{$id} soft-deleted.";
    }
    header('Location: students.php');
    exit;
}

// Action: Handle CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $rows = $pdo->query("SELECT roll_number, full_name, email, phone, department, semester, gpa, status FROM students WHERE status != 'deleted'")->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="students_' . date('Ymd_His') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Roll', 'Name', 'Email', 'Phone', 'Department', 'Semester', 'GPA', 'Status']);
    foreach ($rows as $r) fputcsv($out, $r);
    fclose($out);
    exit;
}

$search = trim($_GET['search'] ?? '');
$dept   = trim($_GET['dept'] ?? 'ALL');

$where = ["status != 'deleted'"];
$params = [];

if ($search !== '') {
    $where[] = "(roll_number LIKE :s1 OR full_name LIKE :s2 OR email LIKE :s3)";
    $params[':s1'] = "%{$search}%";
    $params[':s2'] = "%{$search}%";
    $params[':s3'] = "%{$search}%";
}
if ($dept !== '' && $dept !== 'ALL') {
    $where[] = "department = :d";
    $params[':d'] = $dept;
}

$whereSql = 'WHERE ' . implode(' AND ', $where);
$stmt = $pdo->prepare("SELECT * FROM students {$whereSql} ORDER BY id ASC");
$stmt->execute($params);
$students = $stmt->fetchAll();

$msg = $_SESSION['msg'] ?? '';
unset($_SESSION['msg']);

require_once __DIR__ . '/includes/header.php';
?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:20px;">
        <div>
            <h1>Student Management CRUD</h1>
            <p style="color:var(--text-secondary);">Search, filter, update records, soft-delete, and export student datasets.</p>
        </div>
        <div>
            <a href="students.php?export=csv" class="btn btn-success btn-sm">⬇ Export CSV</a>
        </div>
    </div>

    <?php if (!empty($msg)): ?>
        <div class="alert alert-success">✓ <?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <form action="students.php" method="GET" style="display:grid; grid-template-columns: 2fr 1fr auto; gap:12px; margin-bottom:20px; align-items:end;">
        <div class="form-group" style="margin:0;">
            <label for="search">🔍 Search Students</label>
            <input type="text" id="search" name="search" class="form-control" placeholder="Search by roll number, name, email..." value="<?= htmlspecialchars($search) ?>">
        </div>

        <div class="form-group" style="margin:0;">
            <label for="dept">Department</label>
            <select id="dept" name="dept" class="form-control">
                <option value="ALL">All Departments</option>
                <option value="Information Technology" <?= $dept === 'Information Technology' ? 'selected' : '' ?>>Information Technology</option>
                <option value="Computer Engineering" <?= $dept === 'Computer Engineering' ? 'selected' : '' ?>>Computer Engineering</option>
                <option value="Computer Science & Engineering" <?= $dept === 'Computer Science & Engineering' ? 'selected' : '' ?>>Computer Science &amp; Eng</option>
            </select>
        </div>

        <div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="students.php" class="btn btn-outline" style="margin-left:4px;">Reset</a>
        </div>
    </form>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Roll Number</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Department</th>
                    <th>GPA</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($students)): ?>
                    <tr><td colspan="8" style="text-align:center; padding:30px;">No students found matching your criteria.</td></tr>
                <?php else: ?>
                    <?php foreach ($students as $s): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($s['roll_number']) ?></strong></td>
                            <td><?= htmlspecialchars($s['full_name']) ?></td>
                            <td><?= htmlspecialchars($s['email']) ?></td>
                            <td><?= htmlspecialchars($s['phone']) ?></td>
                            <td><?= htmlspecialchars($s['department']) ?></td>
                            <td><strong style="color:var(--primary);"><?= number_format((float)$s['gpa'], 2) ?></strong></td>
                            <td><span class="badge badge-<?= htmlspecialchars($s['status']) ?>"><?= htmlspecialchars($s['status']) ?></span></td>
                            <td style="text-align:right;">
                                <a href="students.php?action=delete&id=<?= $s['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Soft-delete student <?= htmlspecialchars(addslashes($s['full_name'])) ?>?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
