<?php
require_once __DIR__ . '/auth_check.php';

// Enforce that user is authenticated and possesses the 'student' role
$currentUser = checkAuth('student');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 10 - Student Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="top-nav">
        <div>
            <strong>🎓 StudentHub Portal</strong>
            <span class="badge badge-student" style="margin-left: 10px;">Role: Student</span>
        </div>
        <div>
            <span style="margin-right: 15px; color: #cbd5e1;">Welcome, <strong><?= htmlspecialchars($currentUser['username']) ?></strong></span>
            <a href="logout.php" class="btn btn-danger">Sign Out</a>
        </div>
    </header>

    <main class="dashboard-card">
        <h1>Student Personal Portal</h1>
        <p style="color: var(--text-secondary); margin-bottom: 25px;">Protected Route Guard: Student Session Verified.</p>

        <div class="alert alert-success">
            <strong>✓ Welcome Back:</strong> Hello <?= htmlspecialchars($currentUser['username']) ?> (<code><?= htmlspecialchars($currentUser['email']) ?></code>)! You are logged in with student permissions.
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-top: 25px;">
            <div style="background: #f1f5f9; padding: 20px; border-radius: 8px; border: 1px solid var(--border);">
                <div style="font-size: 0.85rem; font-weight: bold; color: var(--text-secondary);">ACCOUNT STATUS</div>
                <div style="font-size: 1.5rem; font-weight: bold; color: #16a34a;">Active</div>
            </div>
            <div style="background: #f1f5f9; padding: 20px; border-radius: 8px; border: 1px solid var(--border);">
                <div style="font-size: 0.85rem; font-weight: bold; color: var(--text-secondary);">INACTIVITY LIMIT</div>
                <div style="font-size: 1.5rem; font-weight: bold; color: var(--primary);">15 Minutes</div>
            </div>
            <div style="background: #f1f5f9; padding: 20px; border-radius: 8px; border: 1px solid var(--border);">
                <div style="font-size: 0.85rem; font-weight: bold; color: var(--text-secondary);">REMEMBER-ME</div>
                <div style="font-size: 1.5rem; font-weight: bold; color: #8b5cf6;">Supported</div>
            </div>
        </div>

        <div style="margin-top: 25px;">
            <p><strong>RBAC Test:</strong> Try navigating to <a href="admin_dashboard.php" style="color:var(--primary); font-weight:bold;">admin_dashboard.php</a>. You will be redirected because your role is <code>student</code>.</p>
        </div>
    </main>
</body>
</html>
