<?php
$pageTitle = 'About Us';
require_once __DIR__ . '/includes/header.php';
?>

<div class="card">
    <h1>About StudentHub Platform</h1>
    <p style="color:var(--text-secondary); margin-top:10px;">
        StudentHub is the unified web portal engineered at the Faculty of Technology & Engineering (FTE), CHARUSAT for the course Web Development Frameworks (ITUE203).
    </p>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:20px;">
    <div class="card">
        <h3>CSPIT - Information Technology</h3>
        <p style="color:var(--text-secondary); margin-top:8px;">Core expertise in distributed cloud systems, secure database architectures, and REST API frameworks.</p>
    </div>
    <div class="card">
        <h3>CSPIT - Computer Engineering</h3>
        <p style="color:var(--text-secondary); margin-top:8px;">Excellence in data structures, algorithms, system engineering, and artificial intelligence.</p>
    </div>
    <div class="card">
        <h3>DEPSTAR - CSE / IT</h3>
        <p style="color:var(--text-secondary); margin-top:8px;">Focus on innovative open-source solutions, agile design methodologies, and team collaboration.</p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
