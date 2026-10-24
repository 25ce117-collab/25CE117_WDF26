<?php
$pageTitle = 'Feedback';
require_once __DIR__ . '/includes/header.php';

$sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sent = true;
}
?>

<div class="card" style="max-width:600px; margin:20px auto;">
    <h1>Portal Experience Feedback</h1>
    <p style="color:var(--text-secondary); margin-bottom:20px;">Help us enhance StudentHub by sharing your feedback.</p>

    <?php if ($sent): ?>
        <div class="alert alert-success">✓ Thank you for your feedback! Your evaluation has been recorded.</div>
    <?php endif; ?>

    <form action="feedback.php" method="POST">
        <div class="form-group">
            <label for="rating">Rating (1 to 5 Stars)</label>
            <select id="rating" name="rating" class="form-control">
                <option value="5">⭐⭐⭐⭐⭐ Excellent (5 Stars)</option>
                <option value="4">⭐⭐⭐⭐ Good (4 Stars)</option>
                <option value="3">⭐⭐⭐ Satisfactory (3 Stars)</option>
            </select>
        </div>
        <div class="form-group">
            <label for="comments">Comments &amp; Suggestions</label>
            <textarea id="comments" name="comments" class="form-control" rows="4" required placeholder="Write your suggestions..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Submit Feedback</button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
