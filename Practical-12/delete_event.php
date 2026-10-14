<?php
/**
 * Practical 12: Delete Event Handler
 */

session_start();
require_once __DIR__ . '/db.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['error'] = 'Invalid event identifier.';
    header('Location: events.php');
    exit;
}

$pdo = Database::getConnection();

try {
    // 1. Fetch poster path to unlink file
    $stmt = $pdo->prepare("SELECT title, poster_path FROM events WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $event = $stmt->fetch();

    if ($event) {
        if (!empty($event['poster_path']) && file_exists(__DIR__ . '/' . $event['poster_path'])) {
            @unlink(__DIR__ . '/' . $event['poster_path']);
        }

        // 2. Delete Event Record
        $del = $pdo->prepare("DELETE FROM events WHERE id = :id");
        $del->execute([':id' => $id]);

        // 3. Audit Log (Advanced Extension)
        try {
            $audit = $pdo->prepare("INSERT INTO audit_logs (action, details, ip_address) VALUES ('EVENT_DELETED', :details, :ip)");
            $audit->execute([
                ':details' => "Event #{$id} '{$event['title']}' deleted by administrator.",
                ':ip'      => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);
        } catch (Exception $e) { /* ignore */ }

        $_SESSION['success'] = "Event '{$event['title']}' and its associated poster file were deleted.";
    } else {
        $_SESSION['error'] = 'Event not found.';
    }
} catch (PDOException $e) {
    $_SESSION['error'] = 'Database error during deletion: ' . $e->getMessage();
}

header('Location: events.php');
exit;
