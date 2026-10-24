<?php
$pageTitle = 'User RBAC Management';
require_once __DIR__ . '/includes/auth_guard.php';

$currentUser = checkAuth('admin');
$pdo = Database::getConnection();

// Action: AJAX status toggle endpoint
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_id'])) {
    $uid = (int)$_POST['toggle_id'];
    $stmt = $pdo->prepare("SELECT status FROM users WHERE id = :id");
    $stmt->execute([':id' => $uid]);
    $u = $stmt->fetch();
    if ($u) {
        $next = $u['status'] === 'active' ? 'inactive' : 'active';
        $pdo->prepare("UPDATE users SET status = :st WHERE id = :id")->execute([':st' => $next, ':id' => $uid]);
        $pdo->prepare("INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (1, 'STATUS_TOGGLED', :dt, :ip)")
            ->execute([':dt' => "User #{$uid} toggled to {$next}", ':ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1']);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'newStatus' => $next]);
        exit;
    }
}

$users = $pdo->query("SELECT * FROM users ORDER BY id ASC")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="card">
    <h1>User Account Governance &amp; RBAC</h1>
    <p style="color:var(--text-secondary); margin-bottom:20px;">Role-Based Access Control and quick account status toggling.</p>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th style="text-align:right;">Status Toggle</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><strong>#<?= $u['id'] ?></strong></td>
                        <td><?= htmlspecialchars($u['username']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><span class="badge badge-<?= htmlspecialchars($u['role']) ?>"><?= htmlspecialchars($u['role']) ?></span></td>
                        <td>
                            <span id="badge-<?= $u['id'] ?>" class="badge badge-<?= htmlspecialchars($u['status']) ?>">
                                <?= strtoupper($u['status']) ?>
                            </span>
                        </td>
                        <td><small><?= htmlspecialchars($u['last_login'] ?? 'Never') ?></small></td>
                        <td style="text-align:right;">
                            <?php if ($u['id'] == 1): ?>
                                <small style="color:var(--text-secondary);">Superadmin</small>
                            <?php else: ?>
                                <button class="btn btn-outline btn-sm" onclick="toggleStatus(<?= $u['id'] ?>)">
                                    Toggle State
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
async function toggleStatus(uid) {
    try {
        const formData = new FormData();
        formData.append('toggle_id', uid);
        const res = await fetch('admin_users.php', { method: 'POST', body: formData });
        const json = await res.json();
        if (json.status === 'success') {
            const b = document.getElementById('badge-' + uid);
            b.className = 'badge badge-' + json.newStatus;
            b.textContent = json.newStatus.toUpperCase();
        }
    } catch (e) {
        alert('Status toggle failed.');
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
