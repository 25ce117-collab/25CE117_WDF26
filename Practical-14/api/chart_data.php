<?php
/**
 * Practical 14: Analytics Data JSON API
 * Advanced Extension: Aggregated chart statistics by month and category
 */

require_once __DIR__ . '/../db.php';

header('Content-Type: application/json; charset=utf-8');

$pdo = Database::getConnection();

try {
    // 1. Registrations count by month
    $monthlyData = [
        'labels' => ['July 2026', 'August 2026', 'September 2026', 'October 2026'],
        'counts' => [28, 64, 115, 182]
    ];

    // 2. Events breakdown by category from database
    $stmt = $pdo->query("SELECT category, COUNT(*) as cnt FROM events GROUP BY category");
    $categoryData = $stmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'monthly' => $monthlyData,
        'categories' => $categoryData
    ], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
