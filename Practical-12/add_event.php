<?php
/**
 * Practical 12: Add Event with Secure Poster Upload
 * Advanced Extension: MIME Type Validation, Image Renaming & Audit Logging
 */

session_start();
require_once __DIR__ . '/db.php';

$errors = [];

// Ensure upload directory exists
$uploadDir = __DIR__ . '/uploads/posters';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

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

    if (empty($title)) $errors[] = 'Event title is required.';
    if (empty($category)) $errors[] = 'Category selection is required.';
    if (empty($eventDate)) $errors[] = 'Event date is required.';
    if (empty($venue)) $errors[] = 'Venue is required.';

    // Poster Upload Validation
    $posterRelativePath = null;
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['poster'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'File upload failed with system code: ' . $file['error'];
        } else {
            // 1. File Size Verification (Max 2MB = 2097152 bytes)
            const MAX_FILE_SIZE = 2097152;
            if ($file['size'] > MAX_FILE_SIZE) {
                $errors[] = 'Poster file exceeds maximum allowed size of 2 MB.';
            }

            // 2. MIME Type Verification using PHP Fileinfo extension
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            $allowedMimes = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp'
            ];

            if (!array_key_exists($mimeType, $allowedMimes)) {
                $errors[] = "Invalid file type ({$mimeType}). Only JPG, PNG, and WebP images are permitted.";
            } else {
                // 3. Advanced Extension: Cryptographic Image Renaming
                $ext = $allowedMimes[$mimeType];
                $uniqueFileName = 'event_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
                $destination = $uploadDir . '/' . $uniqueFileName;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $posterRelativePath = 'uploads/posters/' . $uniqueFileName;
                } else {
                    $errors[] = 'Failed to move uploaded poster to storage directory.';
                }
            }
        }
    }

    if (empty($errors)) {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                INSERT INTO events (title, category, department, description, event_date, event_time, venue, max_capacity, registration_fee, poster_path, status)
                VALUES (:title, :cat, :dept, :desc, :edate, :etime, :venue, :cap, :fee, :poster, 'upcoming')
            ");
            $stmt->execute([
                ':title'  => $title,
                ':cat'    => $category,
                ':dept'   => $department,
                ':desc'   => $description,
                ':edate'  => $eventDate,
                ':etime'  => $eventTime,
                ':venue'  => $venue,
                ':cap'    => $maxCapacity,
                ':fee'    => $fee,
                ':poster' => $posterRelativePath
            ]);
            $eventId = $pdo->lastInsertId();

            // Advanced Extension: Audit Log Entry
            try {
                $audit = $pdo->prepare("INSERT INTO audit_logs (action, details, ip_address) VALUES ('EVENT_CREATED', :details, :ip)");
                $audit->execute([
                    ':details' => "Admin created new event #{$eventId}: '{$title}' ({$category}) with poster: " . ($posterRelativePath ?: 'None'),
                    ':ip'      => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
                ]);
            } catch (Exception $e) { /* ignore audit failure */ }

            $_SESSION['success'] = "Event '{$title}' successfully published with validated poster!";
            header('Location: events.php');
            exit;

        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event - Practical 12</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container" style="max-width: 700px;">
        <div class="card">
            <h1>Schedule New Campus Event</h1>
            <p style="color:var(--text-secondary); margin-bottom: 20px;">Upload poster image with file type (JPG/PNG/WebP) &amp; file size (&le;2MB) validation.</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="add_event.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Event Title *</label>
                    <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Charusat HackFest 2026" required>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;" class="form-group">
                    <div>
                        <label for="category">Category *</label>
                        <select id="category" name="category" class="form-control" required>
                            <option value="Hackathon">Hackathon</option>
                            <option value="Workshop">Workshop</option>
                            <option value="Symposium">Symposium</option>
                            <option value="Cultural">Cultural Meet</option>
                            <option value="Sports">Sports</option>
                            <option value="Technical">Technical Demo</option>
                        </select>
                    </div>
                    <div>
                        <label for="department">Target Department</label>
                        <select id="department" name="department" class="form-control">
                            <option value="All">All University Students</option>
                            <option value="IT">Information Technology</option>
                            <option value="CE">Computer Engineering</option>
                            <option value="CSE">Computer Science &amp; Eng</option>
                        </select>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;" class="form-group">
                    <div>
                        <label for="event_date">Event Date *</label>
                        <input type="date" id="event_date" name="event_date" class="form-control" value="2026-11-20" required>
                    </div>
                    <div>
                        <label for="event_time">Event Time *</label>
                        <input type="time" id="event_time" name="event_time" class="form-control" value="10:00" required>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 2fr 1fr 1fr; gap:15px;" class="form-group">
                    <div>
                        <label for="venue">Venue *</label>
                        <input type="text" id="venue" name="venue" class="form-control" placeholder="e.g. CSPIT Lab 3" required>
                    </div>
                    <div>
                        <label for="max_capacity">Capacity</label>
                        <input type="number" id="max_capacity" name="max_capacity" class="form-control" value="100" min="1" required>
                    </div>
                    <div>
                        <label for="registration_fee">Fee (₹)</label>
                        <input type="number" id="registration_fee" name="registration_fee" class="form-control" value="0.00" step="10">
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Event Description</label>
                    <textarea id="description" name="description" class="form-control" rows="3" placeholder="Provide full event overview and requirements..."></textarea>
                </div>

                <!-- Poster File Upload with File Type & Size Validation -->
                <div class="form-group" style="background:#f1f5f9; padding:15px; border-radius:6px; border:1px dashed var(--border);">
                    <label for="poster">🖼️ Event Promotional Poster (Max 2 MB, JPG / PNG / WebP)</label>
                    <input type="file" id="poster" name="poster" class="form-control" accept="image/jpeg,image/png,image/webp">
                    <small style="color:var(--text-secondary); display:block; margin-top:5px;">Files are verified by MIME type via <code>finfo_file()</code> and sanitized with randomized filenames.</small>
                </div>

                <div style="display:flex; justify-content:space-between; margin-top:25px;">
                    <a href="events.php" class="btn btn-secondary">← Back to Events</a>
                    <button type="submit" class="btn btn-primary">Publish Event</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
