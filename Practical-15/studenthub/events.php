<?php
$pageTitle = 'Campus Events';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/config/db.php';

$pdo = Database::getConnection();

// Action: Handle Event Registration if requested
$rsvpSuccess = '';
$rsvpError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'rsvp') {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php?msg=login_required');
        exit;
    }

    $eventId = (int)($_POST['event_id'] ?? 0);
    $userId  = (int)$_SESSION['user_id'];

    // Find student ID linked with this user
    $sStmt = $pdo->prepare("SELECT id FROM students WHERE user_id = :uid LIMIT 1");
    $sStmt->execute([':uid' => $userId]);
    $student = $sStmt->fetch();

    if (!$student) {
        $rsvpError = 'Only registered students can reserve event tickets. Please complete your student profile.';
    } else {
        $studentId = $student['id'];

        // Check if already registered
        $chk = $pdo->prepare("SELECT id FROM registrations WHERE student_id = :sid AND event_id = :eid");
        $chk->execute([':sid' => $studentId, ':eid' => $eventId]);

        if ($chk->fetch()) {
            $rsvpError = 'You are already registered for this event.';
        } else {
            // Generate ticket code
            $ticketCode = 'TKT-' . sprintf('%03d', $eventId) . '-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

            try {
                $ins = $pdo->prepare("
                    INSERT INTO registrations (student_id, event_id, ticket_code, attendance_status)
                    VALUES (:sid, :eid, :tkt, 'registered')
                ");
                $ins->execute([
                    ':sid' => $studentId,
                    ':eid' => $eventId,
                    ':tkt' => $ticketCode
                ]);
                $rsvpSuccess = "Seat confirmed! Your ticket verification code is <strong>{$ticketCode}</strong>.";
            } catch (Exception $e) {
                $rsvpError = 'Booking failed: ' . $e->getMessage();
            }
        }
    }
}

// Search and filter parameters
$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? 'ALL');

$where = [];
$params = [];

if ($search !== '') {
    $where[] = "(title LIKE :s1 OR venue LIKE :s2 OR description LIKE :s3)";
    $params[':s1'] = "%{$search}%";
    $params[':s2'] = "%{$search}%";
    $params[':s3'] = "%{$search}%";
}

if ($category !== '' && $category !== 'ALL') {
    $where[] = "category = :cat";
    $params[':cat'] = $category;
}

$whereSql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
$stmt = $pdo->prepare("SELECT * FROM events {$whereSql} ORDER BY event_date ASC");
$stmt->execute($params);
$events = $stmt->fetchAll();

$catStmt = $pdo->query("SELECT DISTINCT category FROM events ORDER BY category ASC");
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:20px;">
        <div>
            <h1>Campus Events &amp; Workshops</h1>
            <p style="color:var(--text-secondary);">Explore technical symposiums, hackathons, and cultural fests at CHARUSAT.</p>
        </div>
        <?php if (($userRole ?? '') === 'admin'): ?>
            <a href="admin_events.php" class="btn btn-primary">+ Manage &amp; Add Events</a>
        <?php endif; ?>
    </div>

    <?php if (!empty($rsvpSuccess)): ?>
        <div class="alert alert-success">✓ <?= $rsvpSuccess ?></div>
    <?php endif; ?>
    <?php if (!empty($rsvpError)): ?>
        <div class="alert alert-danger">⚠️ <?= htmlspecialchars($rsvpError) ?></div>
    <?php endif; ?>

    <!-- Filter Bar -->
    <form action="events.php" method="GET" style="display:grid; grid-template-columns: 2fr 1fr auto; gap:15px; align-items:end; margin-bottom:25px;">
        <div class="form-group" style="margin-bottom:0;">
            <label for="search">🔍 Search Events</label>
            <input type="text" id="search" name="search" class="form-control" placeholder="Search by title, venue..." value="<?= htmlspecialchars($search) ?>">
        </div>

        <div class="form-group" style="margin-bottom:0;">
            <label for="category">Category</label>
            <select id="category" name="category" class="form-control">
                <option value="ALL">All Categories</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= htmlspecialchars($c) ?>" <?= $category === $c ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="events.php" class="btn btn-outline" style="margin-left:5px;">Reset</a>
        </div>
    </form>

    <!-- Events Grid -->
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:20px;">
        <?php if (empty($events)): ?>
            <div style="grid-column: 1/-1; text-align:center; padding:40px; color:var(--text-secondary);">
                🔍 No events found matching your search.
            </div>
        <?php else: ?>
            <?php foreach ($events as $ev): ?>
                <article class="card" style="box-shadow:var(--shadow-md); display:flex; flex-direction:column; justify-content:space-between;">
                    <div>
                        <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                            <span class="badge" style="background:#e0e7ff; color:#3730a3;"><?= htmlspecialchars($ev['category']) ?></span>
                            <span class="badge badge-<?= htmlspecialchars($ev['status']) ?>"><?= htmlspecialchars($ev['status']) ?></span>
                        </div>
                        <h2 style="font-size:1.3rem; margin-bottom:8px;"><?= htmlspecialchars($ev['title']) ?></h2>
                        <p style="color:var(--text-secondary); font-size:0.95rem; margin-bottom:12px;"><?= htmlspecialchars($ev['description']) ?></p>
                        
                        <div style="font-size:0.85rem; color:var(--text-secondary); margin-bottom:15px; border-top:1px solid var(--border); padding-top:10px;">
                            <p>📅 <strong>Date:</strong> <?= htmlspecialchars($ev['event_date']) ?> at <?= htmlspecialchars($ev['event_time']) ?></p>
                            <p>📍 <strong>Venue:</strong> <?= htmlspecialchars($ev['venue']) ?></p>
                            <p>👥 <strong>Capacity:</strong> <?= (int)$ev['max_capacity'] ?> seats</p>
                        </div>
                    </div>

                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:10px;">
                        <span style="font-weight:bold; font-size:1.1rem; color:var(--primary);">
                            <?= (float)$ev['registration_fee'] == 0 ? '<span style="color:var(--success);">FREE</span>' : '₹' . number_format($ev['registration_fee'], 2) ?>
                        </span>

                        <?php if ($ev['status'] === 'upcoming'): ?>
                            <form action="events.php" method="POST" style="margin:0;">
                                <input type="hidden" name="action" value="rsvp">
                                <input type="hidden" name="event_id" value="<?= $ev['id'] ?>">
                                <button type="submit" class="btn btn-primary btn-sm">Reserve Seat</button>
                            </form>
                        <?php else: ?>
                            <span class="badge badge-inactive">Registrations Closed</span>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
