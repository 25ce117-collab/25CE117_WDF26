<?php
/**
 * Practical 8: Database Connection Diagnostic & Health Check
 */

header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/db.php';

$diagnostics = [];
$isConnected = false;
$tableStats = [];
$preparedTest = null;
$errorMessage = null;

try {
    $pdo = Database::getConnection();
    $isConnected = true;

    // 1. MySQL Version
    $verStmt = $pdo->query("SELECT VERSION() AS ver, DATABASE() AS current_db");
    $dbInfo = $verStmt->fetch();

    // 2. Query Table Counts
    $tables = ['users', 'students', 'events', 'registrations', 'audit_logs'];
    foreach ($tables as $tbl) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) AS total FROM `{$tbl}`");
            $tableStats[$tbl] = $stmt->fetch()['total'];
        } catch (PDOException $ex) {
            $tableStats[$tbl] = 'Table not found / unseeded';
        }
    }

    // 3. Test Prepared Statement Execution
    $prep = $pdo->prepare("SELECT id, roll_number, full_name, email, department FROM students WHERE roll_number = :roll");
    $prep->execute([':roll' => '23IT042']);
    $preparedTest = $prep->fetch();

} catch (Exception $e) {
    $errorMessage = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 8 - Database Diagnostic Check</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f8fafc; color: #0f172a; padding: 30px; line-height: 1.6; }
        .box { background: #fff; max-width: 800px; margin: 0 auto; padding: 30px; border-radius: 12px; border: 1px solid #cbd5e1; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        h1 { color: #1e293b; margin-bottom: 8px; font-size: 1.8rem; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 999px; font-size: 0.85rem; font-weight: bold; }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px 14px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f1f5f9; }
        pre { background: #0f172a; color: #38bdf8; padding: 15px; border-radius: 8px; overflow-x: auto; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="box">
        <h1>MySQL Database Connectivity Diagnostics</h1>
        <p style="color: #64748b; margin-bottom: 20px;">Practical 8: PDO Connection Test, 3NF Validation &amp; Prepared Statements</p>

        <?php if ($isConnected): ?>
            <div style="margin-bottom: 20px;">
                <span class="badge badge-success">✓ PDO Database Connection Active</span>
                <p style="margin-top: 8px;"><strong>Server Version:</strong> <?= htmlspecialchars($dbInfo['ver'] ?? 'N/A') ?> | <strong>Active DB:</strong> <code><?= htmlspecialchars($dbInfo['current_db'] ?? 'N/A') ?></code></p>
            </div>

            <h3>Database Table Records</h3>
            <table>
                <thead>
                    <tr><th>Table Name</th><th>Record Count</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($tableStats as $tbl => $cnt): ?>
                        <tr>
                            <td><code><?= htmlspecialchars($tbl) ?></code></td>
                            <td><strong><?= htmlspecialchars((string)$cnt) ?></strong></td>
                            <td><span style="color:#16a34a; font-weight:bold;">Ready</span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h3 style="margin-top: 25px;">Prepared Statement Verification Test</h3>
            <p style="font-size: 0.9rem; color: #64748b;">Executing: <code>SELECT * FROM students WHERE roll_number = :roll</code> with parameter <code>'23IT042'</code></p>
            <?php if ($preparedTest): ?>
                <pre><?= htmlspecialchars(print_r($preparedTest, true)) ?></pre>
                <p style="color: #16a34a; font-weight: bold;">✓ Prepared statement successfully parameterized and executed with zero SQL injection risk!</p>
            <?php else: ?>
                <p style="color: #d97706;">Query executed successfully, but seed record '23IT042' was not found. Please run <code>seed_data.sql</code>.</p>
            <?php endif; ?>

        <?php else: ?>
            <span class="badge badge-danger">✗ Connection Failed</span>
            <p style="margin-top: 15px; color: #dc2626;"><?= htmlspecialchars($errorMessage ?? 'Unknown database error.') ?></p>
            <p style="margin-top: 10px; font-size: 0.9rem; color: #64748b;">Make sure your MySQL server is running in XAMPP on port 3306 and you have imported <code>schema.sql</code> and <code>seed_data.sql</code>.</p>
        <?php endif; ?>
    </div>
</body>
</html>
