<?php
require_once __DIR__ . '/../config/config.php';

$currentPage = basename($_SERVER['PHP_SELF']);
$userRole = $_SESSION['role'] ?? 'guest';
$userName = $_SESSION['username'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : '' ?><?= APP_NAME ?></title>
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <header class="site-header" role="banner">
        <div class="nav-container">
            <a href="index.php" class="brand-logo">🎓 <?= APP_NAME ?></a>

            <nav aria-label="Main Navigation">
                <ul class="nav-links">
                    <li><a href="index.php" class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">Home</a></li>
                    <li><a href="about.php" class="<?= $currentPage === 'about.php' ? 'active' : '' ?>">About</a></li>
                    <li><a href="events.php" class="<?= $currentPage === 'events.php' ? 'active' : '' ?>">Events</a></li>

                    <?php if ($userRole === 'admin'): ?>
                        <li><a href="admin_dashboard.php" class="<?= $currentPage === 'admin_dashboard.php' ? 'active' : '' ?>">Admin KPI</a></li>
                        <li><a href="students.php" class="<?= $currentPage === 'students.php' ? 'active' : '' ?>">Students CRUD</a></li>
                        <li><a href="admin_events.php" class="<?= $currentPage === 'admin_events.php' ? 'active' : '' ?>">Event Scheduler</a></li>
                        <li><a href="admin_users.php" class="<?= $currentPage === 'admin_users.php' ? 'active' : '' ?>">Users RBAC</a></li>
                        <li><a href="audit_logs.php" class="<?= $currentPage === 'audit_logs.php' ? 'active' : '' ?>">Audit Logs</a></li>
                    <?php elseif ($userRole === 'student'): ?>
                        <li><a href="student_dashboard.php" class="<?= $currentPage === 'student_dashboard.php' ? 'active' : '' ?>">My Dashboard</a></li>
                        <li><a href="faq.php" class="<?= $currentPage === 'faq.php' ? 'active' : '' ?>">FAQ</a></li>
                        <li><a href="contact.php" class="<?= $currentPage === 'contact.php' ? 'active' : '' ?>">Support</a></li>
                    <?php else: ?>
                        <li><a href="faq.php" class="<?= $currentPage === 'faq.php' ? 'active' : '' ?>">FAQ</a></li>
                        <li><a href="contact.php" class="<?= $currentPage === 'contact.php' ? 'active' : '' ?>">Contact</a></li>
                    <?php endif; ?>
                </ul>
            </nav>

            <div class="nav-actions">
                <button class="theme-toggle-btn" id="themeToggleBtn">🌙 Dark</button>

                <?php if ($userRole !== 'guest'): ?>
                    <span style="font-size: 0.85rem; font-weight: 600;">👤 <?= htmlspecialchars($userName) ?></span>
                    <a href="logout.php" class="btn btn-outline btn-sm" style="color:var(--danger); border-color:var(--danger);">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary btn-sm">Login</a>
                    <a href="register.php" class="btn btn-success btn-sm">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main id="main-content" class="container" role="main">
