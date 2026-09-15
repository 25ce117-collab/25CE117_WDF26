<?php
/**
 * Practical 7: PHP Form Processing Engine
 * Validates, sanitizes, guards with CSRF, and writes to CSV and JSON with LOCK_EX
 */

require_once __DIR__ . '/csrf.php';

// 1. Guard HTTP Request Method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Method Not Allowed: Form must be submitted via HTTP POST.');
}

// 2. Validate CSRF Token (Advanced Extension)
if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    $_SESSION['errors'] = ['Security verification failed (Invalid or expired CSRF token). Please try again.'];
    $_SESSION['old_input'] = $_POST;
    header('Location: index.php');
    exit;
}

// 3. Extract and Sanitize Inputs
$fullName      = trim(filter_input(INPUT_POST, 'fullName', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
$email         = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '');
$phone         = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
$department    = trim(filter_input(INPUT_POST, 'department', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
$eventInterest = trim(filter_input(INPUT_POST, 'eventInterest', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
$notes         = trim(filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');

$errors = [];

// 4. Server-Side Validation Rules
if (empty($fullName) || !preg_match('/^[A-Za-z\s]{3,50}$/', $fullName)) {
    $errors[] = 'Full Name must contain only letters and spaces (3 to 50 characters).';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid institutional email address is required.';
}

if (empty($phone) || !preg_match('/^[6-9]\d{9}$/', $phone)) {
    $errors[] = 'Mobile number must be a valid 10-digit number starting with 6, 7, 8, or 9.';
}

$validDepts = ['Information Technology', 'Computer Engineering', 'Computer Science & Engineering', 'Electronics & Comm'];
if (empty($department) || !in_array($department, $validDepts)) {
    $errors[] = 'Please select a valid academic department from the list.';
}

if (empty($eventInterest)) {
    $errors[] = 'Please select an event of interest.';
}

if (strlen($notes) > 500) {
    $errors[] = 'Additional notes cannot exceed 500 characters.';
}

// If validation errors exist, redirect back with error feedback
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old_input'] = $_POST;
    header('Location: index.php');
    exit;
}

// 5. Safe File Storage with LOCK_EX
$timestamp = date('Y-m-d H:i:s');
$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

$record = [
    'timestamp'     => $timestamp,
    'fullName'      => $fullName,
    'email'         => $email,
    'phone'         => $phone,
    'department'    => $department,
    'eventInterest' => $eventInterest,
    'notes'         => $notes
];

// --- 5A. Safe CSV Storage ---
$csvFile = $dataDir . '/registrations.csv';
$csvExists = file_exists($csvFile);
$fp = fopen($csvFile, 'a');

if ($fp && flock($fp, LOCK_EX)) {
    // Write CSV header if file is newly created
    if (!$csvExists || filesize($csvFile) === 0) {
        fputcsv($fp, ['Timestamp', 'FullName', 'Email', 'Phone', 'Department', 'EventInterest', 'Notes']);
    }
    fputcsv($fp, array_values($record));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
} else {
    $errors[] = 'Unable to acquire exclusive file lock on CSV storage.';
}

// --- 5B. Safe JSON Storage ---
$jsonFile = $dataDir . '/registrations.json';
$existingData = [];

if (file_exists($jsonFile)) {
    $jsonContent = file_get_contents($jsonFile);
    if ($jsonContent !== false) {
        $decoded = json_decode($jsonContent, true);
        if (is_array($decoded)) {
            $existingData = $decoded;
        }
    }
}

$existingData[] = $record;
$encodedJson = json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

// Write JSON using atomic file_put_contents with LOCK_EX
if (file_put_contents($jsonFile, $encodedJson, LOCK_EX) === false) {
    $errors[] = 'Unable to persist record to JSON storage.';
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old_input'] = $_POST;
    header('Location: index.php');
    exit;
}

// Unset CSRF token after successful submission to prevent replay
unset($_SESSION['csrf_token']);
unset($_SESSION['old_input']);

$_SESSION['success'] = "Registration successfully processed and safely recorded in both CSV and JSON file repositories at {$timestamp}!";
header('Location: records.php');
exit;
