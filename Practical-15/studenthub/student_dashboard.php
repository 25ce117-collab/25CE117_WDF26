<?php
$pageTitle = 'Student Dashboard';
require_once __DIR__ . '/includes/auth_guard.php';

// Verify student authentication
$currentUser = checkAuth('student');
$pdo = Database::getConnection();

// Fetch student profile record
$sStmt = $pdo->prepare("SELECT * FROM students WHERE user_id = :uid LIMIT 1");
$sStmt->execute([':uid' => $currentUser['id']]);
$student = $sStmt->fetch();

// Fetch enrolled events
$enrolled = [];
if ($student) {
    $eStmt = $pdo->prepare("
        SELECT r.id AS reg_id, r.ticket_code, r.attendance_status, r.registered_at,
               e.id AS event_id, e.title, e.category, e.event_date, e.event_time, e.venue
        FROM registrations r
        INNER JOIN events e ON r.event_id = e.id
        WHERE r.student_id = :sid
        ORDER BY e.event_date ASC
    ");
    $eStmt->execute([':sid' => $student['id']]);
    $enrolled = $eStmt->fetchAll();
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="card" style="margin-bottom:25px;">
    <h1>Welcome, <?= htmlspecialchars($student['full_name'] ?? $currentUser['username']) ?>!</h1>
    <p style="color:var(--text-secondary);">Student ID: <strong><?= htmlspecialchars($student['roll_number'] ?? 'N/A') ?></strong> | Department: <?= htmlspecialchars($student['department'] ?? 'Information Technology') ?> | Semester 3</p>
</div>

<!-- Metrics Summary -->
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:15px; margin-bottom:25px;">
    <div class="card" style="padding:18px; margin:0;">
        <span style="font-size:0.8rem; font-weight:bold; color:var(--text-secondary); text-transform:uppercase;">Registered Events</span>
        <div style="font-size:2rem; font-weight:800; color:var(--primary);"><?= count($enrolled) ?> Active</div>
    </div>
    <div class="card" style="padding:18px; margin:0;">
        <span style="font-size:0.8rem; font-weight:bold; color:var(--text-secondary); text-transform:uppercase;">Academic CGPA</span>
        <div style="font-size:2rem; font-weight:800; color:var(--success);"><?= htmlspecialchars((string)($student['gpa'] ?? '9.65')) ?></div>
    </div>
    <div class="card" style="padding:18px; margin:0;">
        <span style="font-size:0.8rem; font-weight:bold; color:var(--text-secondary); text-transform:uppercase;">Attendance Standing</span>
        <div style="font-size:2rem; font-weight:800; color:#8b5cf6;">100%</div>
    </div>
</div>

<!-- Registered Events List -->
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
        <h2>My Event Bookings &amp; Verification Tickets</h2>
        <a href="events.php" class="btn btn-outline btn-sm">+ Browse Events</a>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Ticket Code</th>
                    <th>Event Title</th>
                    <th>Category</th>
                    <th>Event Schedule</th>
                    <th>Venue</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($enrolled)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding:30px; color:var(--text-secondary);">
                            You have not registered for any upcoming events yet. <a href="events.php">Browse upcoming events</a>.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($enrolled as $item): ?>
                        <tr>
                            <td><strong style="color:var(--primary); font-family:monospace;"><?= htmlspecialchars($item['ticket_code']) ?></strong></td>
                            <td><strong><?= htmlspecialchars($item['title']) ?></strong></td>
                            <td><span class="badge" style="background:#e0e7ff; color:#3730a3;"><?= htmlspecialchars($item['category']) ?></span></td>
                            <td>📅 <?= htmlspecialchars($item['event_date']) ?> at <?= htmlspecialchars($item['event_time']) ?></td>
                            <td>📍 <?= htmlspecialchars($item['venue']) ?></td>
                            <td><span class="badge badge-active"><?= htmlspecialchars($item['attendance_status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
