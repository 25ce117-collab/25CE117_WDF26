<?php
$pageTitle = 'Event Scheduler';
require_once __DIR__ . '/includes/auth_guard.php';

$currentUser = checkAuth('admin');
$pdo = Database::getConnection();

$uploadDir = __DIR__ . '/uploads/posters';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

$errors = [];
$success = '';

// Handle Event Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $category    = trim($_POST['category'] ?? '');
    $date        = trim($_POST['date'] ?? '');
    $venue       = trim($_POST['venue'] ?? '');
    $capacity    = (int)($_POST['capacity'] ?? 100);
    $description = trim($_POST['description'] ?? '');

    $posterPath = null;
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['poster'];
        if ($file['size'] <= 2097152) { // 2MB
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            $mimes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            if (isset($mimes[$mime])) {
                $unique = 'event_' . bin2hex(random_bytes(6)) . '.' . $mimes[$mime];
                if (move_uploaded_file($file['tmp_name'], $uploadDir . '/' . $unique)) {
                    $posterPath = 'uploads/posters/' . $unique;
                }
            }
        }
    }

    if (!empty($title) && !empty($date) && !empty($venue)) {
        $stmt = $pdo->prepare("
            INSERT INTO events (title, category, event_date, event_time, venue, max_capacity, description, poster_path, status)
            VALUES (:title, :cat, :date, '10:00:00', :venue, :cap, :desc, :poster, 'upcoming')
        ");
        $stmt->execute([
            ':title'  => $title,
            ':cat'    => $category,
            ':date'   => $date,
            ':venue'  => $venue,
            ':cap'    => $capacity,
            ':desc'   => $description,
            ':poster' => $posterPath
        ]);
        $success = "Event '{$title}' published successfully!";
    }
}

// Fetch all events
$events = $pdo->query("SELECT * FROM events ORDER BY event_date ASC")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="card">
    <h1>Campus Event Scheduler &amp; Poster Upload</h1>
    <p style="color:var(--text-secondary); margin-bottom:20px;">Schedule events with strict MIME type validation (JPG, PNG, WebP &le; 2MB).</p>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">✓ <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form action="admin_events.php" method="POST" enctype="multipart/form-data" style="background:#f8fafc; padding:20px; border-radius:8px; border:1px solid var(--border); margin-bottom:30px;">
        <h3 style="margin-bottom:15px;">Create New Campus Event</h3>
        <div style="display:grid; grid-template-columns:2fr 1fr; gap:12px;" class="form-group">
            <div>
                <label for="title">Event Title *</label>
                <input type="text" id="title" name="title" class="form-control" required placeholder="e.g. AI Symposium 2026">
            </div>
            <div>
                <label for="category">Category *</label>
                <select id="category" name="category" class="form-control" required>
                    <option value="Hackathon">Hackathon</option>
                    <option value="Workshop">Workshop</option>
                    <option value="Symposium">Symposium</option>
                    <option value="Cultural">Cultural</option>
                </select>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px;" class="form-group">
            <div>
                <label for="date">Event Date *</label>
                <input type="date" id="date" name="date" class="form-control" value="2026-11-20" required>
            </div>
            <div>
                <label for="venue">Venue *</label>
                <input type="text" id="venue" name="venue" class="form-control" placeholder="CSPIT Lab 3" required>
            </div>
            <div>
                <label for="capacity">Seat Capacity *</label>
                <input type="number" id="capacity" name="capacity" class="form-control" value="100" required>
            </div>
        </div>

        <div class="form-group">
            <label for="poster">Event Poster Image (Max 2MB - Verified via MIME Inspection)</label>
            <input type="file" id="poster" name="poster" class="form-control" accept="image/jpeg,image/png,image/webp">
        </div>

        <div class="form-group">
            <label for="description">Event Summary</label>
            <textarea id="description" name="description" class="form-control" rows="2" placeholder="Brief outline..."></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Schedule &amp; Publish</button>
    </form>

    <h2>Active Campus Events Roster</h2>
    <div class="table-wrapper" style="margin-top:15px;">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th>Venue</th>
                    <th>Capacity</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $ev): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($ev['title']) ?></strong></td>
                        <td><span class="badge" style="background:#e0e7ff; color:#3730a3;"><?= htmlspecialchars($ev['category']) ?></span></td>
                        <td><?= htmlspecialchars($ev['event_date']) ?></td>
                        <td><?= htmlspecialchars($ev['venue']) ?></td>
                        <td><?= (int)$ev['max_capacity'] ?> seats</td>
                        <td><span class="badge badge-<?= htmlspecialchars($ev['status']) ?>"><?= htmlspecialchars($ev['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
