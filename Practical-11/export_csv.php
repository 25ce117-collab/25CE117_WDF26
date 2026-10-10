<?php
/**
 * Practical 11: Export Student Directory to CSV
 * Intermediate Extension: Direct HTTP CSV Streaming
 */

require_once __DIR__ . '/db.php';

$search     = trim($_GET['search'] ?? '');
$department = trim($_GET['department'] ?? 'ALL');
$status     = trim($_GET['status'] ?? 'ALL');

$pdo = Database::getConnection();

// Build WHERE Clause matching active search / filters
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
}

$whereSql = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

$stmt = $pdo->prepare("
    SELECT roll_number, full_name, email, phone, department, semester, gpa, city, status, created_at 
    FROM students 
    {$whereSql} 
    ORDER BY id ASC
");
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Send CSV Download Headers
$filename = 'students_export_' . date('Y-m-d_His') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');

// Output CSV Header
fputcsv($output, ['Roll Number', 'Full Name', 'Institutional Email', 'Mobile', 'Department', 'Semester', 'GPA', 'City', 'Status', 'Registration Date']);

// Output Data Rows
foreach ($students as $row) {
    fputcsv($output, $row);
}

fclose($output);
exit;
