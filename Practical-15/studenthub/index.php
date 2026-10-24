<?php
$pageTitle = 'Home';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/config/db.php';

$pdo = Database::getConnection();

// Fetch 3 upcoming events for landing showcase
$events = [];
try {
    $stmt = $pdo->query("SELECT * FROM events WHERE status = 'upcoming' ORDER BY event_date ASC LIMIT 3");
    $events = $stmt->fetchAll();
} catch (Exception $e) { /* fallback */ }
?>

<section class="card" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #fff; padding: 40px 30px;">
    <span style="background: rgba(37,99,235,0.25); color: #60a5fa; padding: 4px 12px; border-radius: 999px; font-size: 0.85rem; font-weight: 600;">CHARUSAT Student Ecosystem</span>
    <h1 style="font-size: 2.4rem; margin: 10px 0 15px; color: #fff;">Welcome to StudentHub Portal</h1>
    <p style="color: #94a3b8; font-size: 1.1rem; max-width: 650px; margin-bottom: 25px;">
        The centralized university platform integrating student academic records, campus workshops, hackathons, and administrative oversight under modern Web Development Frameworks.
    </p>
    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
        <a href="events.php" class="btn btn-primary">Explore Campus Events &rarr;</a>
        <a href="register.php" class="btn btn-outline" style="color: #fff; border-color: #475569;">Register Account</a>
    </div>
</section>

<section>
    <h2 style="margin: 25px 0 15px;">Featured Upcoming Events</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        <?php if (empty($events)): ?>
            <p>No upcoming events currently scheduled.</p>
        <?php else: ?>
            <?php foreach ($events as $ev): ?>
                <article class="card">
                    <span class="badge" style="background:#e0e7ff; color:#3730a3;"><?= htmlspecialchars($ev['category']) ?></span>
                    <h3 style="margin: 8px 0;"><?= htmlspecialchars($ev['title']) ?></h3>
                    <p style="color: var(--text-secondary); font-size: 0.95rem;"><?= htmlspecialchars(substr($ev['description'], 0, 90)) ?>...</p>
                    <p style="margin: 10px 0; font-size: 0.85rem;">📅 <?= htmlspecialchars($ev['event_date']) ?> | 📍 <?= htmlspecialchars($ev['venue']) ?></p>
                    <a href="events.php" class="btn btn-outline btn-block">View Details &amp; RSVP</a>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
