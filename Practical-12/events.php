<?php
/**
 * Practical 12: Event Management CRUD with Poster Upload & File Validation
 * Intermediate Extension: Category & Open/Closed Status Filter
 */

session_start();
require_once __DIR__ . '/db.php';

$pdo = Database::getConnection();

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

// Filter Parameters
$search   = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? 'ALL');
$status   = trim($_GET['status'] ?? 'ALL');

$whereClauses = [];
$params = [];

if ($search !== '') {
    $whereClauses[] = "(title LIKE :s1 OR venue LIKE :s2 OR description LIKE :s3)";
    $params[':s1'] = "%{$search}%";
    $params[':s2'] = "%{$search}%";
    $params[':s3'] = "%{$search}%";
}

if ($category !== '' && $category !== 'ALL') {
    $whereClauses[] = "category = :cat";
    $params[':cat'] = $category;
}

if ($status !== '' && $status !== 'ALL') {
    $whereClauses[] = "status = :st";
    $params[':st'] = $status;
}

$whereSql = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

$stmt = $pdo->prepare("SELECT * FROM events {$whereSql} ORDER BY event_date ASC");
$stmt->execute($params);
$events = $stmt->fetchAll();

// Distinct categories for filter dropdown
$catStmt = $pdo->query("SELECT DISTINCT category FROM events ORDER BY category ASC");
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Event Management CRUD (Practical 12)</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <div>
                <h1>📅 Campus Event Management</h1>
                <p style="color:var(--text-secondary);">Practical 12: Event CRUD, Poster Upload Validation, Directory Protection &amp; Category Filters</p>
            </div>
            <a href="add_event.php" class="btn btn-success">+ Schedule New Event</a>
        </header>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">✓ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Search & Category / Open-Closed Filter (Intermediate Extension) -->
        <div class="card">
            <form action="events.php" method="GET" class="filter-bar">
                <div class="form-group" style="flex: 2;">
                    <label for="search">🔍 Search Events</label>
                    <input type="text" id="search" name="search" class="form-control" 
                           placeholder="Search by title, venue, keywords..." value="<?= htmlspecialchars($search) ?>">
                </div>

                <div class="form-group">
                    <label for="category">Category Filter</label>
                    <select id="category" name="category" class="form-control">
                        <option value="ALL">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Event Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="ALL" <?= $status === 'ALL' ? 'selected' : '' ?>>All Statuses</option>
                        <option value="upcoming" <?= $status === 'upcoming' ? 'selected' : '' ?>>Upcoming (Open)</option>
                        <option value="ongoing" <?= $status === 'ongoing' ? 'selected' : '' ?>>Ongoing</option>
                        <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Completed (Closed)</option>
                        <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary" style="padding: 10px 16px;">Apply Filter</button>
                    <a href="events.php" class="btn btn-secondary btn-sm" style="margin-left: 5px;">Reset</a>
                </div>
            </form>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Poster</th>
                            <th>Event Details</th>
                            <th>Category</th>
                            <th>Schedule &amp; Venue</th>
                            <th>Capacity</th>
                            <th>Fee</th>
                            <th>Status</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($events)): ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 30px; color: var(--text-secondary);">
                                    🔍 No events found matching your filter criteria.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($events as $ev): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($ev['poster_path']) && file_exists(__DIR__ . '/' . $ev['poster_path'])): ?>
                                            <img src="<?= htmlspecialchars($ev['poster_path']) ?>" alt="Poster" class="poster-thumb">
                                        <?php else: ?>
                                            <div class="poster-placeholder">🖼️</div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($ev['title']) ?></strong>
                                        <p style="font-size:0.85rem; color:var(--text-secondary); max-width:260px;"><?= htmlspecialchars(substr($ev['description'], 0, 75)) ?>...</p>
                                    </td>
                                    <td><span class="badge" style="background:#e0e7ff; color:#3730a3;"><?= htmlspecialchars($ev['category']) ?></span></td>
                                    <td>
                                        <div>📅 <?= htmlspecialchars($ev['event_date']) ?></div>
                                        <small style="color:var(--text-secondary);">📍 <?= htmlspecialchars($ev['venue']) ?></small>
                                    </td>
                                    <td><strong><?= (int)$ev['max_capacity'] ?></strong> seats</td>
                                    <td><?= (float)$ev['registration_fee'] == 0 ? '<span style="color:#16a34a; font-weight:bold;">FREE</span>' : '₹' . number_format($ev['registration_fee'], 2) ?></td>
                                    <td>
                                        <span class="badge badge-<?= htmlspecialchars($ev['status']) ?>">
                                            <?= htmlspecialchars($ev['status']) ?>
                                        </span>
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <a href="edit_event.php?id=<?= $ev['id'] ?>" class="btn btn-secondary btn-sm">✏️ Edit</a>
                                        <a href="delete_event.php?id=<?= $ev['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete event \'<?= htmlspecialchars(addslashes($ev['title'])) ?>\' and remove its poster file?');">🗑️ Delete</a>
                                    </td>
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
