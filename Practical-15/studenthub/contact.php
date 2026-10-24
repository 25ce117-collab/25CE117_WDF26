<?php
$pageTitle = 'Contact Us';
require_once __DIR__ . '/includes/header.php';

$sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sent = true;
}
?>

<div class="card" style="max-width:650px; margin:20px auto;">
    <h1>Contact StudentHub Help Desk</h1>
    <p style="color:var(--text-secondary); margin-bottom:20px;">Faculty of Technology &amp; Engineering, CHARUSAT</p>

    <?php if ($sent): ?>
        <div class="alert alert-success">✓ Thank you! Your inquiry has been transmitted to the coordinator.</div>
    <?php endif; ?>

    <form action="contact.php" method="POST">
        <div class="form-group">
            <label for="name">Your Name</label>
            <input type="text" id="name" name="name" class="form-control" required placeholder="Full Name">
        </div>
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" required placeholder="user@charusat.edu.in">
        </div>
        <div class="form-group">
            <label for="msg">Message / Inquiry</label>
            <textarea id="msg" name="msg" class="form-control" rows="4" required placeholder="Type your inquiry..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Send Inquiry</button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
