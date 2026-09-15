<?php
require_once __DIR__ . '/csrf.php';

$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

$csvPath = __DIR__ . '/data/registrations.csv';
$jsonPath = __DIR__ . '/data/registrations.json';

// Read CSV Records
$csvRows = [];
if (file_exists($csvPath)) {
    if (($handle = fopen($csvPath, 'r')) !== false) {
        $header = fgetcsv($handle);
        while (($data = fgetcsv($handle)) !== false) {
            $csvRows[] = $data;
        }
        fclose($handle);
    }
}

// Read JSON Records
$jsonRecords = [];
if (file_exists($jsonPath)) {
    $content = file_get_contents($jsonPath);
    if ($content) {
        $jsonRecords = json_decode($content, true) ?: [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Stored Records Display (Practical 7)</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <nav class="nav-bar">
            <strong>🎓 StudentHub Portal</strong>
            <div>
                <a href="index.php">📝 Registration Form</a>
                <a href="records.php" class="active">📊 Stored Records (CSV/JSON)</a>
            </div>
        </nav>

        <header class="header">
            <h1>Stored Registrations Repository</h1>
            <p>Intermediate Extension: Dynamic Parsing &amp; Web Display of CSV &amp; JSON Flat Files</p>
        </header>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <strong>✓ Operation Completed:</strong> <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <!-- JSON File Data View -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h2>1. Records from <code>data/registrations.json</code> (<?= count($jsonRecords) ?>)</h2>
                <a href="data/registrations.json" target="_blank" class="btn" style="background:#0284c7; padding:6px 14px; font-size:0.85rem;">View Raw JSON ↗</a>
            </div>

            <?php if (empty($jsonRecords)): ?>
                <p style="color:var(--text-secondary);">No records found in JSON file.</p>
            <?php else: ?>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Department</th>
                                <th>Event</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_reverse($jsonRecords) as $item): ?>
                                <tr>
                                    <td><small><?= htmlspecialchars($item['timestamp']) ?></small></td>
                                    <td><strong><?= htmlspecialchars($item['fullName']) ?></strong></td>
                                    <td><?= htmlspecialchars($item['email']) ?></td>
                                    <td><?= htmlspecialchars($item['phone']) ?></td>
                                    <td><?= htmlspecialchars($item['department']) ?></td>
                                    <td><span style="background:#dbeafe; color:#1e40af; padding:3px 8px; border-radius:4px; font-size:0.8rem; font-weight:600;"><?= htmlspecialchars($item['eventInterest']) ?></span></td>
                                    <td><?= htmlspecialchars($item['notes'] ?: '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- CSV File Data View -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h2>2. Records from <code>data/registrations.csv</code> (<?= count($csvRows) ?>)</h2>
                <a href="data/registrations.csv" download class="btn" style="background:#059669; padding:6px 14px; font-size:0.85rem;">Download CSV ⬇</a>
            </div>

            <?php if (empty($csvRows)): ?>
                <p style="color:var(--text-secondary);">No records found in CSV file.</p>
            <?php else: ?>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Department</th>
                                <th>Event</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_reverse($csvRows) as $row): ?>
                                <tr>
                                    <td><small><?= htmlspecialchars($row[0] ?? '') ?></small></td>
                                    <td><strong><?= htmlspecialchars($row[1] ?? '') ?></strong></td>
                                    <td><?= htmlspecialchars($row[2] ?? '') ?></td>
                                    <td><?= htmlspecialchars($row[3] ?? '') ?></td>
                                    <td><?= htmlspecialchars($row[4] ?? '') ?></td>
                                    <td><span style="background:#dcfce7; color:#166534; padding:3px 8px; border-radius:4px; font-size:0.8rem; font-weight:600;"><?= htmlspecialchars($row[5] ?? '') ?></span></td>
                                    <td><?= htmlspecialchars($row[6] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
