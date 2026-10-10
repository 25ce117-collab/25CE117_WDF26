<?php
/**
 * Practical 11: Student Management CRUD with Search & Filter
 * Intermediate Extension: CSV Export
 * Advanced Extension: Soft delete using status column
 */

session_start();
require_once __DIR__ . '/db.php';

$pdo = Database::getConnection();

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

// Query Filters
$search     = trim($_GET['search'] ?? '');
$department = trim($_GET['department'] ?? 'ALL');
$status     = trim($_GET['status'] ?? 'ALL');
$page       = max(1, (int)($_GET['page'] ?? 1));
$perPage    = 8;
$offset     = ($page - 1) * $perPage;

// Build WHERE Clause dynamically with Prepared Statements
$whereClauses = [];
$params = [];

if ($search !== '') {
    $whereClauses[] = "(roll_number LIKE :s1 OR full_name LIKE :s2 OR email LIKE :s3)";
    $params[':s1'] = "%{$search}%";
    $params[':s2'] = "%{$search}%";
    $params[':s3'] = "%{$search}%";
}

if ($department !== '' && $department !== 'ALL') {
    $whereClauses[] = "department = :dept";
    $params[':dept'] = $department;
}

if ($status !== '' && $status !== 'ALL') {
    $whereClauses[] = "status = :status";
    $params[':status'] = $status;
} else {
    // By default, exclude soft-deleted unless explicitly requested
    if ($status === 'ALL') {
        // show all including deleted, or default to active/inactive
    }
}

$whereSql = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

// Count Total Filtered Rows
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM students {$whereSql}");
$countStmt->execute($params);
$totalRows = (int)$countStmt->fetchColumn();
$totalPages = max(1, ceil($totalRows / $perPage));

// Fetch Paginated Rows
$query = "
    SELECT id, roll_number, full_name, email, phone, department, semester, gpa, city, status 
    FROM students 
    {$whereSql} 
    ORDER BY id ASC 
    LIMIT :limit OFFSET :offset
";
$stmt = $pdo->prepare($query);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$students = $stmt->fetchAll();

// Distinct Departments for dropdown
$deptStmt = $pdo->query("SELECT DISTINCT department FROM students WHERE department IS NOT NULL AND department != '' ORDER BY department ASC");
$allDepts = $deptStmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Student Management CRUD (Practical 11)</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <div>
                <h1>👥 Student Management Directory</h1>
                <p style="color:var(--text-secondary);">Practical 11: Full CRUD, Multi-Criteria Search, Soft Deletion &amp; CSV Export</p>
            </div>
            <div style="display: flex; gap: 10px;">
                <!-- Intermediate Extension: CSV Export -->
                <a href="export_csv.php?search=<?= urlencode($search) ?>&department=<?= urlencode($department) ?>&status=<?= urlencode($status) ?>" class="btn btn-success">
                    ⬇ Export to CSV
                </a>
                <a href="add_student.php" class="btn">
                    + Add New Student
                </a>
            </div>
        </header>

        <!-- Flash Notices -->
        <?php if (!empty($success)): ?>
            <div class="alert alert-success">✓ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Search & Filter Card -->
        <div class="card">
            <form action="index.php" method="GET" class="filter-bar">
                <div class="form-group" style="flex: 2;">
                    <label for="search">🔍 Search Students</label>
                    <input type="text" id="search" name="search" class="form-control" 
                           placeholder="Search by name, roll number, or email..." value="<?= htmlspecialchars($search) ?>">
                </div>

                <div class="form-group">
                    <label for="department">Department</label>
                    <select id="department" name="department" class="form-control">
                        <option value="ALL">All Departments</option>
                        <?php foreach ($allDepts as $d): ?>
                            <option value="<?= htmlspecialchars($d) ?>" <?= $department === $d ? 'selected' : '' ?>>
                                <?= htmlspecialchars($d) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Account Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="ALL" <?= $status === 'ALL' ? 'selected' : '' ?>>All Statuses</option>
                        <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active Only</option>
                        <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive Only</option>
                        <option value="deleted" <?= $status === 'deleted' ? 'selected' : '' ?>>Deleted (Soft-Deleted)</option>
                    </select>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary" style="padding: 10px 16px;">Apply Filter</button>
                    <a href="index.php" class="btn btn-secondary btn-sm" style="margin-left: 5px;">Reset</a>
                </div>
            </form>

            <!-- Table View -->
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Roll Number</th>
                            <th>Student Name</th>
                            <th>Email Address</th>
                            <th>Mobile</th>
                            <th>Department</th>
                            <th>Sem</th>
                            <th>GPA</th>
                            <th>Status</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 30px; color: var(--text-secondary);">
                                    🔍 No student records match your query. Try resetting filters.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($students as $s): ?>
                                <tr class="<?= $s['status'] === 'deleted' ? 'row-deleted' : '' ?>">
                                    <td><strong><?= htmlspecialchars($s['roll_number']) ?></strong></td>
                                    <td><?= htmlspecialchars($s['full_name']) ?></td>
                                    <td><?= htmlspecialchars($s['email']) ?></td>
                                    <td><?= htmlspecialchars($s['phone']) ?></td>
                                    <td><?= htmlspecialchars($s['department']) ?></td>
                                    <td>Sem <?= (int)$s['semester'] ?></td>
                                    <td><strong style="color:var(--primary);"><?= number_format((float)$s['gpa'], 2) ?></strong></td>
                                    <td>
                                        <span class="badge badge-<?= htmlspecialchars($s['status']) ?>">
                                            <?= htmlspecialchars($s['status']) ?>
                                        </span>
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <a href="edit_student.php?id=<?= $s['id'] ?>" class="btn btn-secondary btn-sm" title="Edit Student">✏️ Edit</a>
                                        
                                        <?php if ($s['status'] === 'deleted'): ?>
                                            <!-- Advanced Extension: Restore Soft-Deleted Record -->
                                            <a href="delete_student.php?id=<?= $s['id'] ?>&action=restore" class="btn btn-success btn-sm" onclick="return confirm('Restore this soft-deleted student to active status?');">
                                                ♻️ Restore
                                            </a>
                                        <?php else: ?>
                                            <!-- Advanced Extension: Soft Delete with JavaScript Confirmation -->
                                            <a href="delete_student.php?id=<?= $s['id'] ?>&action=soft_delete" class="btn btn-danger btn-sm" onclick="return confirm('Confirm soft deletion for <?= htmlspecialchars(addslashes($s['full_name'])) ?>? The record can be restored later.');">
                                                🗑️ Delete
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <div class="pagination">
                <span style="font-size: 0.9rem; color: var(--text-secondary);">
                    Showing <?= $totalRows > 0 ? $offset + 1 : 0 ?> to <?= min($offset + $perPage, $totalRows) ?> of <?= $totalRows ?> total students
                </span>

                <div class="page-links">
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <a href="index.php?page=<?= $p ?>&search=<?= urlencode($search) ?>&department=<?= urlencode($department) ?>&status=<?= urlencode($status) ?>" 
                           class="page-link <?= $p === $page ? 'active' : '' ?>">
                            <?= $p ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
