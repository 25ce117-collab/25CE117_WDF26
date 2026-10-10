<?php
/**
 * Practical 11: Delete / Soft-Delete Student Handler
 * Advanced Extension: Soft delete using status column instead of permanent purge
 */

session_start();
require_once __DIR__ . '/db.php';

$id     = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? 'soft_delete';

if ($id <= 0) {
    $_SESSION['error'] = 'Invalid student record identifier.';
    header('Location: index.php');
    exit;
}

$pdo = Database::getConnection();

try {
    if ($action === 'restore') {
        // Restore soft-deleted student
        $stmt = $pdo->prepare("UPDATE students SET status = 'active' WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $_SESSION['success'] = "Student record #{$id} restored to active status!";
    } elseif ($action === 'hard_delete') {
        // Permanent deletion
        $stmt = $pdo->prepare("DELETE FROM students WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $_SESSION['success'] = "Student record #{$id} permanently removed from database.";
    } else {
        // Default: Soft Delete (Sets status = 'deleted')
        $stmt = $pdo->prepare("UPDATE students SET status = 'deleted' WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $_SESSION['success'] = "Student record #{$id} soft-deleted. It can be viewed under Deleted filter and restored at any time.";
    }
} catch (PDOException $e) {
    $_SESSION['error'] = 'Operation failed: ' . $e->getMessage();
}

header('Location: index.php');
exit;
