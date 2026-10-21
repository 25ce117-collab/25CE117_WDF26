<?php
/**
 * Practical 14: User Management with Role Badges & AJAX Status Toggle
 * Intermediate Extension: Active/Inactive status toggle
 */

session_start();
require_once __DIR__ . '/db.php';

$pdo = Database::getConnection();

$stmt = $pdo->query("SELECT id, username, email, role, status, last_login, created_at FROM users ORDER BY id ASC");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - User Management &amp; RBAC (Practical 14)</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="admin-navbar">
        <a href="dashboard.php" class="brand-title">🛡️ StudentHub Admin Console</a>
        <nav>
            <ul class="nav-menu">
                <li><a href="dashboard.php">📊 Analytics Dashboard</a></li>
                <li><a href="users.php" class="active">👥 User Management</a></li>
                <li><a href="audit_logs.php">📜 Audit Trail</a></li>
            </ul>
        </nav>
    </header>

    <div class="layout-container">
        <div class="card">
            <div class="card-header">
                <div>
                    <h1>Portal Users &amp; Role-Based Governance</h1>
                    <p style="color:var(--text-secondary);">Intermediate Extension: Instant AJAX Account Status Toggle with Audit Log Recording</p>
                </div>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Username</th>
                            <th>Email Address</th>
                            <th>Assigned Role</th>
                            <th>Current Status</th>
                            <th>Last Login</th>
                            <th style="text-align: right;">AJAX Quick Toggle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><strong>#<?= $u['id'] ?></strong></td>
                                <td><?= htmlspecialchars($u['username']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td>
                                    <span class="badge badge-<?= htmlspecialchars($u['role']) ?>">
                                        <?= htmlspecialchars($u['role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span id="status-badge-<?= $u['id'] ?>" class="badge badge-<?= htmlspecialchars($u['status']) ?>">
                                        <?= strtoupper($u['status']) ?>
                                    </span>
                                </td>
                                <td><small><?= htmlspecialchars($u['last_login'] ?? 'Never') ?></small></td>
                                <td style="text-align: right;">
                                    <?php if ($u['id'] == 1): ?>
                                        <small style="color:var(--text-secondary);">Protected Superadmin</small>
                                    <?php else: ?>
                                        <!-- Intermediate Extension: AJAX Status Toggle Button -->
                                        <button class="btn-toggle btn-toggle-status" 
                                                data-id="<?= $u['id'] ?>" 
                                                data-status="<?= $u['status'] ?>">
                                            <?= $u['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="js/dashboard.js"></script>
</body>
</html>
