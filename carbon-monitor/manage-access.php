<?php
/**
 * CARBON[6] User Access Management
 * Suspend, unsuspend, or revoke installation access
 */

// Simple password protection
$DASHBOARD_PASSWORD = 'Carbon6Admin2026';

session_start();

if (!isset($_SESSION['authenticated'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['password'] === $DASHBOARD_PASSWORD) {
        $_SESSION['authenticated'] = true;
    } else {
        header('Location: dashboard.php');
        exit;
    }
}

// Get installations
$db = new PDO('sqlite:' . __DIR__ . '/carbon_telemetry.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Add status column if needed
try {
    $db->exec("ALTER TABLE installations ADD COLUMN status TEXT DEFAULT 'active'");
} catch (PDOException $e) {
    // Column exists
}

$statusFilter = $_GET['status'] ?? 'all';

$query = "SELECT i.*,
          (SELECT COUNT(*) FROM heartbeats h WHERE h.install_id = i.install_id) as heartbeat_count,
          (SELECT h.timestamp FROM heartbeats h WHERE h.install_id = i.install_id ORDER BY timestamp DESC LIMIT 1) as last_seen
          FROM installations i";

if ($statusFilter !== 'all') {
    $query .= " WHERE i.status = :status";
}

$query .= " ORDER BY i.timestamp DESC";

$stmt = $db->prepare($query);
if ($statusFilter !== 'all') {
    $stmt->bindParam(':status', $statusFilter);
}
$stmt->execute();
$installations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count by status
$counts = [
    'active' => $db->query("SELECT COUNT(*) FROM installations WHERE status = 'active' OR status IS NULL")->fetchColumn(),
    'suspended' => $db->query("SELECT COUNT(*) FROM installations WHERE status = 'suspended'")->fetchColumn(),
    'revoked' => $db->query("SELECT COUNT(*) FROM installations WHERE status = 'revoked'")->fetchColumn(),
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CARBON[6] - Access Management</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0D0D0D;
            color: #ffffff;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #FF3366 0%, #CC0044 100%);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            font-size: 36px;
        }
        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 12px 24px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
        }
        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        .stat-box {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid rgba(255, 51, 102, 0.2);
        }
        .stat-box h3 {
            font-size: 14px;
            color: #999;
            margin-bottom: 10px;
        }
        .stat-box .count {
            font-size: 32px;
            font-weight: 700;
            color: #FF3366;
        }
        .filters {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        .filter-btn {
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 51, 102, 0.3);
            border-radius: 8px;
            color: white;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }
        .filter-btn.active {
            background: linear-gradient(135deg, #FF3366 0%, #CC0044 100%);
            border-color: transparent;
        }
        .filter-btn:hover {
            background: rgba(255, 51, 102, 0.2);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            overflow: hidden;
        }
        th {
            background: rgba(255, 51, 102, 0.2);
            padding: 15px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid rgba(255, 51, 102, 0.3);
        }
        td {
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        tr:hover {
            background: rgba(255, 255, 255, 0.02);
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            display: inline-block;
        }
        .status-active {
            background: rgba(0, 255, 0, 0.2);
            color: #00FF00;
        }
        .status-suspended {
            background: rgba(255, 165, 0, 0.2);
            color: #FFA500;
        }
        .status-revoked {
            background: rgba(255, 0, 0, 0.2);
            color: #FF4444;
        }
        .action-btn {
            padding: 8px 16px;
            margin: 2px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
        }
        .btn-suspend {
            background: #FFA500;
            color: white;
        }
        .btn-unsuspend {
            background: #00CC66;
            color: white;
        }
        .btn-revoke {
            background: #FF3366;
            color: white;
        }
        .action-btn:hover {
            opacity: 0.8;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        .modal.active {
            display: flex;
        }
        .modal-content {
            background: #1a1a1a;
            padding: 30px;
            border-radius: 15px;
            max-width: 500px;
            width: 90%;
        }
        .modal-content h2 {
            margin-bottom: 20px;
            color: #FF3366;
        }
        .modal-content textarea {
            width: 100%;
            padding: 12px;
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 51, 102, 0.3);
            border-radius: 8px;
            color: white;
            font-family: inherit;
            min-height: 100px;
            margin-bottom: 20px;
        }
        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid rgba(0, 255, 0, 0.3);
            color: #00FF00;
        }
        .alert-error {
            background: rgba(255, 0, 0, 0.1);
            border: 1px solid rgba(255, 0, 0, 0.3);
            color: #FF4444;
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>🔐 Access Management</h1>
            <p>Control installation permissions</p>
        </div>
        <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
    </div>

    <div id="alertBox"></div>

    <div class="stats-row">
        <div class="stat-box">
            <h3>Active Installations</h3>
            <div class="count"><?= $counts['active'] ?></div>
        </div>
        <div class="stat-box">
            <h3>Suspended</h3>
            <div class="count" style="color: #FFA500"><?= $counts['suspended'] ?></div>
        </div>
        <div class="stat-box">
            <h3>Revoked</h3>
            <div class="count" style="color: #FF4444"><?= $counts['revoked'] ?></div>
        </div>
        <div class="stat-box">
            <h3>Total</h3>
            <div class="count"><?= array_sum($counts) ?></div>
        </div>
    </div>

    <div class="filters">
        <a href="?status=all" class="filter-btn <?= $statusFilter === 'all' ? 'active' : '' ?>">All</a>
        <a href="?status=active" class="filter-btn <?= $statusFilter === 'active' ? 'active' : '' ?>">Active</a>
        <a href="?status=suspended" class="filter-btn <?= $statusFilter === 'suspended' ? 'active' : '' ?>">Suspended</a>
        <a href="?status=revoked" class="filter-btn <?= $statusFilter === 'revoked' ? 'active' : '' ?>">Revoked</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Install ID</th>
                <th>Code</th>
                <th>OS</th>
                <th>Location</th>
                <th>Installed</th>
                <th>Last Seen</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($installations as $inst): ?>
            <tr data-install-id="<?= htmlspecialchars($inst['install_id']) ?>">
                <td><code><?= htmlspecialchars(substr($inst['install_id'], 0, 8)) ?>...</code></td>
                <td><?= htmlspecialchars($inst['install_code'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($inst['os'] ?? 'Unknown') ?></td>
                <td>
                    <?php
                    $location = [];
                    if ($inst['city']) $location[] = $inst['city'];
                    if ($inst['country']) $location[] = $inst['country'];
                    echo htmlspecialchars(implode(', ', $location) ?: 'Unknown');
                    ?>
                </td>
                <td><?= date('M d, Y', strtotime($inst['timestamp'])) ?></td>
                <td>
                    <?php if ($inst['last_seen']): ?>
                        <?= timeAgo($inst['last_seen']) ?>
                    <?php else: ?>
                        Never
                    <?php endif; ?>
                </td>
                <td>
                    <?php
                    $status = $inst['status'] ?? 'active';
                    $class = 'status-' . $status;
                    ?>
                    <span class="status-badge <?= $class ?>"><?= strtoupper($status) ?></span>
                </td>
                <td>
                    <?php if ($status === 'active'): ?>
                        <button class="action-btn btn-suspend" onclick="suspendInstall('<?= htmlspecialchars($inst['install_id']) ?>')">Suspend</button>
                        <button class="action-btn btn-revoke" onclick="showRevokeModal('<?= htmlspecialchars($inst['install_id']) ?>')">Revoke</button>
                    <?php elseif ($status === 'suspended'): ?>
                        <button class="action-btn btn-unsuspend" onclick="unsuspendInstall('<?= htmlspecialchars($inst['install_id']) ?>')">Reactivate</button>
                        <button class="action-btn btn-revoke" onclick="showRevokeModal('<?= htmlspecialchars($inst['install_id']) ?>')">Revoke</button>
                    <?php else: ?>
                        <span style="color: #666;">Permanently Revoked</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Revoke Modal -->
    <div id="revokeModal" class="modal">
        <div class="modal-content">
            <h2>Revoke Installation Access</h2>
            <p style="margin-bottom: 15px; color: #999;">This action is permanent and cannot be undone.</p>
            <textarea id="revokeReason" placeholder="Enter reason for revocation..."></textarea>
            <div class="modal-actions">
                <button class="action-btn" style="background: #666;" onclick="closeRevokeModal()">Cancel</button>
                <button class="action-btn btn-revoke" onclick="confirmRevoke()">Revoke Access</button>
            </div>
        </div>
    </div>

    <script>
        let currentInstallId = null;

        function showAlert(message, type = 'success') {
            const alertBox = document.getElementById('alertBox');
            alertBox.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
            setTimeout(() => { alertBox.innerHTML = ''; }, 5000);
        }

        async function suspendInstall(installId) {
            if (!confirm('Suspend this installation? The user will not be able to send telemetry until reactivated.')) {
                return;
            }

            try {
                const response = await fetch('access-control.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'suspend', install_id: installId })
                });

                const data = await response.json();
                if (data.success) {
                    showAlert('Installation suspended successfully');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert(data.error || 'Failed to suspend', 'error');
                }
            } catch (error) {
                showAlert('Network error: ' + error.message, 'error');
            }
        }

        async function unsuspendInstall(installId) {
            try {
                const response = await fetch('access-control.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'unsuspend', install_id: installId })
                });

                const data = await response.json();
                if (data.success) {
                    showAlert('Installation reactivated successfully');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert(data.error || 'Failed to reactivate', 'error');
                }
            } catch (error) {
                showAlert('Network error: ' + error.message, 'error');
            }
        }

        function showRevokeModal(installId) {
            currentInstallId = installId;
            document.getElementById('revokeModal').classList.add('active');
            document.getElementById('revokeReason').value = '';
        }

        function closeRevokeModal() {
            document.getElementById('revokeModal').classList.remove('active');
            currentInstallId = null;
        }

        async function confirmRevoke() {
            const reason = document.getElementById('revokeReason').value || 'No reason provided';

            try {
                const response = await fetch('access-control.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'revoke',
                        install_id: currentInstallId,
                        reason: reason
                    })
                });

                const data = await response.json();
                if (data.success) {
                    showAlert('Access permanently revoked');
                    closeRevokeModal();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert(data.error || 'Failed to revoke', 'error');
                }
            } catch (error) {
                showAlert('Network error: ' + error.message, 'error');
            }
        }
    </script>
</body>
</html>

<?php
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;

    if ($diff < 60) return $diff . 's ago';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    return floor($diff / 86400) . 'd ago';
}
?>
