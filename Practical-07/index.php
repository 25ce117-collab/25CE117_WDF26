<?php
require_once __DIR__ . '/csrf.php';

$errors = $_SESSION['errors'] ?? [];
$success = $_SESSION['success'] ?? '';
$old = $_SESSION['old_input'] ?? [];

// Clear flash messages
unset($_SESSION['errors'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - PHP Form Processing (Practical 7)</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <nav class="nav-bar">
            <strong>🎓 StudentHub Portal</strong>
            <div>
                <a href="index.php" class="active">📝 Registration Form</a>
                <a href="records.php">📊 Stored Records (CSV/JSON)</a>
            </div>
        </nav>

        <header class="header">
            <h1>PHP Server-Side Form Processing</h1>
            <p>Practical 7: Server Validation, Sanitization, File Locking (LOCK_EX) &amp; CSRF Protection</p>
        </header>

        <!-- Flash Messages -->
        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <strong>✓ Success:</strong> <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <strong>⚠️ Validation Errors Encountered:</strong>
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Form Card -->
        <div class="card">
            <h2 style="margin-bottom: 20px;">Campus Event Pre-Registration</h2>
            
            <form action="process.php" method="POST">
                <!-- CSRF Token (Advanced Extension) -->
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken()) ?>">

                <div class="form-group">
                    <label for="fullName">Full Legal Name *</label>
                    <input type="text" id="fullName" name="fullName" class="form-control" 
                           value="<?= htmlspecialchars($old['fullName'] ?? '') ?>" 
                           placeholder="Letters & spaces only (e.g. Satyajit Parmar)" required>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="email">Institutional Email *</label>
                        <input type="email" id="email" name="email" class="form-control" 
                               value="<?= htmlspecialchars($old['email'] ?? '') ?>" 
                               placeholder="user@charusat.edu.in" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Contact Mobile *</label>
                        <input type="tel" id="phone" name="phone" class="form-control" 
                               value="<?= htmlspecialchars($old['phone'] ?? '') ?>" 
                               placeholder="10-digit number (starts 6-9)" maxlength="10" required>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="department">Academic Department *</label>
                        <select id="department" name="department" class="form-control" required>
                            <option value="">-- Choose Department --</option>
                            <?php 
                            $depts = ['Information Technology', 'Computer Engineering', 'Computer Science & Engineering', 'Electronics & Comm'];
                            foreach ($depts as $d): ?>
                                <option value="<?= $d ?>" <?= ($old['department'] ?? '') === $d ? 'selected' : '' ?>><?= $d ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="eventInterest">Event of Interest *</label>
                        <select id="eventInterest" name="eventInterest" class="form-control" required>
                            <option value="">-- Select Event --</option>
                            <?php 
                            $events = ['HackFest 2026', 'Web Dev Bootcamp', 'Generative AI', 'Cybersecurity Symposium', 'Spoural Cultural Fest'];
                            foreach ($events as $ev): ?>
                                <option value="<?= $ev ?>" <?= ($old['eventInterest'] ?? '') === $ev ? 'selected' : '' ?>><?= $ev ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes">Special Requirements / Notes (Max 500 chars)</label>
                    <textarea id="notes" name="notes" class="form-control" rows="3" 
                              placeholder="Any dietary restrictions, team names, or prerequisite questions..."><?= htmlspecialchars($old['notes'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn btn-block" style="padding: 12px; font-size: 1.05rem;">Submit via Secure HTTP POST</button>
            </form>
        </div>
    </div>
</body>
</html>
