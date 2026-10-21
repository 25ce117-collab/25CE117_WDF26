<?php
/**
 * Practical 14: Security Audit Trail Viewer
 */

session_start();
require_once __DIR__ . '/db.php';

$pdo = Database::getConnection();

// Fetch latest 50 audit entries with optional user join
$stmt = $pdo->query("
    SELECT a.*, u.username, u.email 
    FROM audit_logs a
    LEFT JOIN users u ON a.user_id = u.id
    ORDER BY a.created_at DESC 
    LIMIT 50
");
$logs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Audit Trail Logs (Practical 14)</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="admin-navbar">
        <a href="dashboard.php" class="brand-title">🛡️ StudentHub Admin Console</a>
        <nav>
            <ul class="nav-menu">
                <li><a href="dashboard.php">📊 Analytics Dashboard</a></li>
                <li><a href="users.php">👥 User Management</a></li>
                <li><a href="audit_logs.php" class="active">📜 Audit Trail</a></li>
            </ul>
        </nav>
    </header>

    <div class="layout-container">
        <div class="card">
            <div class="card-header">
                <div>
                    <h1>Security Audit Registry</h1>
                    <p style="color:var(--text-secondary);">Chronological traceability of critical administrative operations and authentication events.</p>
                </div>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Timestamp</th>
                            <th>Action Type</th>
                            <th>Target / Details</th>
                            <th>Performed By</th>
                            <th>Client IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr><td colspan="6" style="text-align:center; padding:30px;">No audit records recorded yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><strong>#<?= $log['id'] ?></strong></td>
                                    <td><small><?= htmlspecialchars($log['created_at']) ?></small></td>
                                    <td><span class="badge" style="background:#e0e7ff; color:#3730a3;"><?= htmlspecialchars($log['action']) ?></span></td>
                                    <td><?= htmlspecialchars($log['details']) ?></td>
                                    <td><strong><?= htmlspecialchars($log['username'] ?? 'System/Guest') ?></strong></td>
                                    <td><code><?= htmlspecialchars($log['ip_address']) ?></code></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
