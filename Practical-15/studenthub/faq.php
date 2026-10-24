<?php
$pageTitle = 'FAQ';
require_once __DIR__ . '/includes/header.php';
?>

<div class="card">
    <h1>Frequently Asked Questions (FAQ)</h1>
    <p style="color:var(--text-secondary); margin-bottom:20px;">Common queries regarding registration, event participation, and account security.</p>

    <div style="display:flex; flex-direction:column; gap:12px;">
        <details class="card" style="margin:0; cursor:pointer;">
            <summary style="font-weight:bold; font-size:1.05rem;">How do I register for an account?</summary>
            <p style="margin-top:10px; color:var(--text-secondary);">Go to the Registration page, provide your CHARUSAT student email (e.g. <code>23it042@charusat.edu.in</code>), create a strong password, and submit.</p>
        </details>

        <details class="card" style="margin:0; cursor:pointer;">
            <summary style="font-weight:bold; font-size:1.05rem;">How is my password secured?</summary>
            <p style="margin-top:10px; color:var(--text-secondary);">All passwords are cryptographically hashed using the Blowfish-based Bcrypt algorithm (cost factor 12) with per-user salts. Plaintext passwords are never stored.</p>
        </details>

        <details class="card" style="margin:0; cursor:pointer;">
            <summary style="font-weight:bold; font-size:1.05rem;">How do I book tickets for workshops and hackathons?</summary>
            <p style="margin-top:10px; color:var(--text-secondary);">Log into your student account, open the Events tab, and click "Reserve Seat" on any active event card to obtain your verification ticket code.</p>
        </details>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
