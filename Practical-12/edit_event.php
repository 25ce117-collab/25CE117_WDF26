<?php
/**
 * Practical 12: Edit Event & Replace Poster
 */

session_start();
require_once __DIR__ . '/db.php';

$pdo = Database::getConnection();
$id = (int)($_GET['id'] ?? 0);
$errors = [];

$stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$event = $stmt->fetch();

if (!$event) {
    $_SESSION['error'] = 'Event record not found.';
    header('Location: events.php');
    exit;
}

$uploadDir = __DIR__ . '/uploads/posters';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $category    = trim($_POST['category'] ?? '');
    $department  = trim($_POST['department'] ?? 'All');
    $description = trim($_POST['description'] ?? '');
    $eventDate   = trim($_POST['event_date'] ?? '');
    $eventTime   = trim($_POST['event_time'] ?? '');
    $venue       = trim($_POST['venue'] ?? '');
    $maxCapacity = (int)($_POST['max_capacity'] ?? 100);
    $fee         = (float)($_POST['registration_fee'] ?? 0.0);
    $status      = trim($_POST['status'] ?? 'upcoming');

    $posterPath = $event['poster_path']; // preserve existing by default

    // Optional New Poster Upload
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['poster'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            if ($file['size'] > 2097152) {
                $errors[] = 'Poster file exceeds 2 MB.';
            } else {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);

                $allowedMimes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
                if (!array_key_exists($mimeType, $allowedMimes)) {
                    $errors[] = 'Invalid file type. Only JPG, PNG, and WebP images are allowed.';
                } else {
                    $ext = $allowedMimes[$mimeType];
                    $uniqueName = 'event_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
                    $dest = $uploadDir . '/' . $uniqueName;

                    if (move_uploaded_file($file['tmp_name'], $dest)) {
                        // Unlink old poster file if existed
                        if (!empty($posterPath) && file_exists(__DIR__ . '/' . $posterPath)) {
                            @unlink(__DIR__ . '/' . $posterPath);
                        }
                        $posterPath = 'uploads/posters/' . $uniqueName;
                    }
                }
            }
        }
    }

    if (empty($errors)) {
        try {
            $upd = $pdo->prepare("
                UPDATE events 
                SET title = :title, category = :cat, department = :dept, description = :desc,
                    event_date = :edate, event_time = :etime, venue = :venue, max_capacity = :cap,
                    registration_fee = :fee, poster_path = :poster, status = :status
                WHERE id = :id
            ");
            $upd->execute([
                ':title'  => $title,
                ':cat'    => $category,
                ':dept'   => $department,
                ':desc'   => $description,
                ':edate'  => $eventDate,
                ':etime'  => $eventTime,
                ':venue'  => $venue,
                ':cap'    => $maxCapacity,
                ':fee'    => $fee,
                ':poster' => $posterPath,
                ':status' => $status,
                ':id'     => $id
            ]);

            $_SESSION['success'] = "Event '{$title}' updated successfully!";
            header('Location: events.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Database update error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - Practical 12</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container" style="max-width: 700px;">
        <div class="card">
            <h1>Edit Event Details</h1>
            <p style="color:var(--text-secondary); margin-bottom: 20px;">Update logistics or upload replacement poster.</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="edit_event.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Event Title</label>
                    <input type="text" id="title" name="title" class="form-control" value="<?= htmlspecialchars($event['title']) ?>" required>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;" class="form-group">
                    <div>
                        <label for="category">Category</label>
                        <select id="category" name="category" class="form-control" required>
                            <?php 
                            $cats = ['Hackathon', 'Workshop', 'Symposium', 'Cultural', 'Sports', 'Technical'];
                            foreach ($cats as $c): ?>
                                <option value="<?= $c ?>" <?= $event['category'] === $c ? 'selected' : '' ?>><?= $c ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="status">Event Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="upcoming" <?= $event['status'] === 'upcoming' ? 'selected' : '' ?>>Upcoming</option>
                            <option value="ongoing" <?= $event['status'] === 'ongoing' ? 'selected' : '' ?>>Ongoing</option>
                            <option value="completed" <?= $event['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="cancelled" <?= $event['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;" class="form-group">
                    <div>
                        <label for="event_date">Event Date</label>
                        <input type="date" id="event_date" name="event_date" class="form-control" value="<?= htmlspecialchars($event['event_date']) ?>" required>
                    </div>
                    <div>
                        <label for="venue">Venue</label>
                        <input type="text" id="venue" name="venue" class="form-control" value="<?= htmlspecialchars($event['venue']) ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="3"><?= htmlspecialchars($event['description']) ?></textarea>
                </div>

                <div class="form-group" style="background:#f1f5f9; padding:15px; border-radius:6px; border:1px dashed var(--border);">
                    <label>Current Poster Image</label>
                    <div style="display:flex; align-items:center; gap:15px; margin:10px 0;">
                        <?php if (!empty($event['poster_path']) && file_exists(__DIR__ . '/' . $event['poster_path'])): ?>
                            <img src="<?= htmlspecialchars($event['poster_path']) ?>" alt="Current Poster" style="width:70px; height:70px; object-fit:cover; border-radius:6px;">
                            <span><code><?= htmlspecialchars($event['poster_path']) ?></code></span>
                        <?php else: ?>
                            <span style="color:var(--text-secondary);">No poster uploaded yet.</span>
                        <?php endif; ?>
                    </div>
                    <label for="poster">Upload New Poster to Replace (Max 2 MB)</label>
                    <input type="file" id="poster" name="poster" class="form-control" accept="image/jpeg,image/png,image/webp">
                </div>

                <div style="display:flex; justify-content:space-between; margin-top:25px;">
                    <a href="events.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
