<?php
require_once __DIR__ . '/auth_check.php';

// Enforce that user is authenticated and possesses the 'admin' role
$currentUser = checkAuth('admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 10 - Administrator Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="top-nav">
        <div>
            <strong>🛡️ StudentHub Administration Console</strong>
            <span class="badge badge-admin" style="margin-left: 10px;">Role: Administrator</span>
        </div>
        <div>
            <span style="margin-right: 15px; color: #cbd5e1;">Logged in as: <strong><?= htmlspecialchars($currentUser['username']) ?></strong></span>
            <a href="logout.php" class="btn btn-danger">Log Out</a>
        </div>
    </header>

    <main class="dashboard-card">
        <h1>Administrator Dashboard</h1>
        <p style="color: var(--text-secondary); margin-bottom: 25px;">Protected Route Guard: Access verified via role-based PHP session checking.</p>

        <div class="alert alert-success">
            <strong>✓ Authentication Verified:</strong> Welcome, Administrator <code><?= htmlspecialchars($currentUser['email']) ?></code>. You have full access to system-level modules.
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-top: 25px;">
            <div style="background: #f1f5f9; padding: 20px; border-radius: 8px; border: 1px solid var(--border);">
                <div style="font-size: 0.85rem; font-weight: bold; color: var(--text-secondary);">CURRENT USER ID</div>
                <div style="font-size: 1.8rem; font-weight: bold; color: var(--primary);"><?= $currentUser['id'] ?></div>
            </div>
            <div style="background: #f1f5f9; padding: 20px; border-radius: 8px; border: 1px solid var(--border);">
                <div style="font-size: 0.85rem; font-weight: bold; color: var(--text-secondary);">SESSION TIMEOUT</div>
                <div style="font-size: 1.8rem; font-weight: bold; color: #16a34a;">15 Minutes</div>
            </div>
            <div style="background: #f1f5f9; padding: 20px; border-radius: 8px; border: 1px solid var(--border);">
                <div style="font-size: 0.85rem; font-weight: bold; color: var(--text-secondary);">REMEMBER-ME TOKEN</div>
                <div style="font-size: 1.4rem; font-weight: bold; color: #8b5cf6;">SHA-256 Hashed</div>
            </div>
        </div>

        <div style="margin-top: 30px; padding: 20px; background: #fff; border: 1px dashed var(--border); border-radius: 8px;">
            <h3>Role-Based Access Verification Note:</h3>
            <p style="color: var(--text-secondary); margin-top: 6px;">If a student user attempts to directly open this URL (<code>admin_dashboard.php</code>), the route guard <code>checkAuth('admin')</code> intercepts the request and redirects them to the login page with an unauthorized access code.</p>
        </div>
    </main>
</body>
</html>
