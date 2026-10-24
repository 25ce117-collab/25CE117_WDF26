<?php
$pageTitle = 'Security Audit Logs';
require_once __DIR__ . '/includes/auth_guard.php';

$currentUser = checkAuth('admin');
$pdo = Database::getConnection();

$logs = $pdo->query("
    SELECT a.*, u.username 
    FROM audit_logs a
    LEFT JOIN users u ON a.user_id = u.id
    ORDER BY a.created_at DESC 
    LIMIT 50
")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="card">
    <h1>Security Audit Trail</h1>
    <p style="color:var(--text-secondary); margin-bottom:20px;">Traceability log documenting system registrations, status changes, and administrative actions.</p>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Log ID</th>
                    <th>Timestamp</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>Initiated By</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $l): ?>
                    <tr>
                        <td><strong>#<?= $l['id'] ?></strong></td>
                        <td><small><?= htmlspecialchars($l['created_at']) ?></small></td>
                        <td><span class="badge" style="background:#e0e7ff; color:#3730a3;"><?= htmlspecialchars($l['action']) ?></span></td>
                        <td><?= htmlspecialchars($l['details']) ?></td>
                        <td><strong><?= htmlspecialchars($l['username'] ?? 'System') ?></strong></td>
                        <td><code><?= htmlspecialchars($l['ip_address']) ?></code></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
