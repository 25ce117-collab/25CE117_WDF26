<?php
/**
 * Practical 14: AJAX User Status Toggle Endpoint
 * Intermediate Extension: Toggle active/inactive state with audit log insertion
 */

session_start();
require_once __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
    exit;
}

$userId    = (int)($_POST['user_id'] ?? 0);
$newStatus = trim($_POST['new_status'] ?? '');

if ($userId <= 0 || !in_array($newStatus, ['active', 'inactive'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid parameters.']);
    exit;
}

$pdo = Database::getConnection();

try {
    $stmt = $pdo->prepare("UPDATE users SET status = :status WHERE id = :id");
    $stmt->execute([':status' => $newStatus, ':id' => $userId]);

    // Record into Audit Log
    $audit = $pdo->prepare("
        INSERT INTO audit_logs (user_id, action, details, ip_address)
        VALUES (:uid, 'USER_STATUS_TOGGLED', :details, :ip)
    ");
    $audit->execute([
        ':uid'     => 1, // Admin ID
        ':details' => "Administrator toggled status of user #{$userId} to '{$newStatus}'",
        ':ip'      => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
    ]);

    echo json_encode([
        'status' => 'success',
        'message' => "User #{$userId} successfully updated to {$newStatus}.",
        'userId' => $userId,
        'newStatus' => $newStatus
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
