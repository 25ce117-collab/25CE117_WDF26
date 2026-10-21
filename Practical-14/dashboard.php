<?php
/**
 * Practical 14: Admin Dashboard with RBAC, Dynamic Menu & Analytics
 * Advanced Extension: HTML5 Canvas Analytics Chart
 */

session_start();
require_once __DIR__ . '/db.php';

// In production, RBAC verification: check if user is admin. For lab demo, default to admin session
if (empty($_SESSION['role'])) {
    $_SESSION['role'] = 'admin';
    $_SESSION['username'] = 'superadmin';
}

$pdo = Database::getConnection();

// 1. SQL Aggregation Queries for KPI Metrics
$totalStudents = (int)$pdo->query("SELECT COUNT(*) FROM students WHERE status != 'deleted'")->fetchColumn();
$totalEvents   = (int)$pdo->query("SELECT COUNT(*) FROM events WHERE status != 'cancelled'")->fetchColumn();
$totalRegs     = (int)$pdo->query("SELECT COUNT(*) FROM registrations")->fetchColumn();
$activeUsers   = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active'")->fetchColumn();

// 2. Latest Event Registrations
$stmt = $pdo->query("
    SELECT r.id, r.ticket_code, r.attendance_status, r.registered_at,
           s.full_name AS student_name, s.roll_number, s.department,
           e.title AS event_title, e.event_date
    FROM registrations r
    INNER JOIN students s ON r.student_id = s.id
    INNER JOIN events e ON r.event_id = e.id
    ORDER BY r.registered_at DESC
    LIMIT 6
");
$latestRegistrations = $stmt->fetchAll();

// Dynamic Menu Items based on Role (RBAC)
$role = $_SESSION['role'] ?? 'guest';
$menu = [
    'dashboard.php'  => '📊 Overview',
    'users.php'      => '👥 Manage Users',
    'audit_logs.php' => '📜 Audit Trail'
];
if ($role === 'student') {
    $menu = [
        '../Practical-10/student_dashboard.php' => '🎓 Student Home',
        '../Practical-06/index.html'           => '📅 Browse Events'
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Admin Analytics Dashboard (Practical 14)</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="admin-navbar">
        <a href="dashboard.php" class="brand-title">🛡️ StudentHub Admin Console</a>
        <nav>
            <ul class="nav-menu">
                <!-- Dynamic Menu Generation Based on User Role -->
                <?php foreach ($menu as $url => $label): ?>
                    <li><a href="<?= $url ?>" class="<?= $url === 'dashboard.php' ? 'active' : '' ?>"><?= $label ?></a></li>
                <?php endforeach; ?>
                <li><a href="../Practical-10/logout.php" style="color:#ef4444;">Sign Out</a></li>
            </ul>
        </nav>
    </header>

    <div class="layout-container">
        <!-- KPI Analytics Metrics -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-title">Active Students</div>
                <div class="kpi-value"><?= number_format($totalStudents) ?></div>
                <div class="kpi-sub">↑ Verified campus directory</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-title">Scheduled Events</div>
                <div class="kpi-value" style="color:#0284c7;"><?= number_format($totalEvents) ?></div>
                <div class="kpi-sub">Technical &amp; cultural fests</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-title">Event Registrations</div>
                <div class="kpi-value" style="color:#16a34a;"><?= number_format($totalRegs) ?></div>
                <div class="kpi-sub">Reserved ticket slots</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-title">Active Users</div>
                <div class="kpi-value" style="color:#8b5cf6;"><?= number_format($activeUsers) ?></div>
                <div class="kpi-sub">RBAC-managed accounts</div>
            </div>
        </div>

        <!-- Advanced Extension: Simple Analytics Chart for Event Registrations -->
        <div class="card">
            <div class="card-header">
                <div>
                    <h2>Event Registrations Growth (Odd Semester 2026)</h2>
                    <p style="color:var(--text-secondary); font-size:0.9rem;">Advanced Extension: HTML5 Canvas Chart dynamically rendering monthly registration data</p>
                </div>
            </div>
            <div class="chart-box" style="text-align:center;">
                <canvas id="analyticsCanvas" width="900" height="260" style="max-width:100%; height:auto;"></canvas>
            </div>
        </div>

        <!-- Latest Event Registrations Table -->
        <div class="card">
            <div class="card-header">
                <h2>Latest Event Registrations</h2>
                <a href="../Practical-11/index.php" class="btn btn-sm">View Directory</a>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Ticket Code</th>
                            <th>Student Name</th>
                            <th>Roll Number</th>
                            <th>Department</th>
                            <th>Event Enrolled</th>
                            <th>Registration Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($latestRegistrations)): ?>
                            <tr><td colspan="7" style="text-align:center; padding:25px;">No registrations recorded.</td></tr>
                        <?php else: ?>
                            <?php foreach ($latestRegistrations as $reg): ?>
                                <tr>
                                    <td><code><?= htmlspecialchars($reg['ticket_code']) ?></code></td>
                                    <td><strong><?= htmlspecialchars($reg['student_name']) ?></strong></td>
                                    <td><?= htmlspecialchars($reg['roll_number']) ?></td>
                                    <td><?= htmlspecialchars($reg['department']) ?></td>
                                    <td><?= htmlspecialchars($reg['event_title']) ?></td>
                                    <td><small><?= htmlspecialchars($reg['registered_at']) ?></small></td>
                                    <td><span class="badge badge-active"><?= htmlspecialchars($reg['attendance_status']) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="js/dashboard.js"></script>
</body>
</html>
