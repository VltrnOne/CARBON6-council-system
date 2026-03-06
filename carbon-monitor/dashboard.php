<?php
/**
 * CARBON[6] Real-Time Monitoring Dashboard
 * Live view of all installations and active users
 */

// Simple password protection
$DASHBOARD_PASSWORD = 'Carbon6Admin2026';

session_start();

if (!isset($_SESSION['authenticated'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['password'] === $DASHBOARD_PASSWORD) {
        $_SESSION['authenticated'] = true;
    } else {
        showLogin();
        exit;
    }
}

// Get stats
$db = new PDO('sqlite:' . __DIR__ . '/carbon_telemetry.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Total installations
$totalInstalls = $db->query("SELECT COUNT(*) FROM installations")->fetchColumn();

// Active in last 24 hours
$activeInstalls = $db->query("SELECT COUNT(DISTINCT install_id) FROM heartbeats
                               WHERE timestamp > datetime('now', '-24 hours')")->fetchColumn();

// Installations in last 24 hours
$recentInstalls = $db->query("SELECT COUNT(*) FROM installations
                               WHERE timestamp > datetime('now', '-24 hours')")->fetchColumn();

// Get recent installations
$recent = $db->query("SELECT * FROM installations
                      ORDER BY timestamp DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

// Get active systems
$active = $db->query("SELECT i.*, h.timestamp as last_seen, h.status, h.uptime_seconds
                      FROM installations i
                      JOIN heartbeats h ON i.install_id = h.install_id
                      WHERE h.timestamp > datetime('now', '-1 hour')
                      GROUP BY i.install_id
                      ORDER BY h.timestamp DESC")->fetchAll(PDO::FETCH_ASSOC);

// OS breakdown
$osList = $db->query("SELECT os, COUNT(*) as count
                      FROM installations
                      WHERE os IS NOT NULL
                      GROUP BY os")->fetchAll(PDO::FETCH_ASSOC);

// Geographic distribution
$countries = $db->query("SELECT country, COUNT(*) as count
                         FROM installations
                         WHERE country IS NOT NULL
                         GROUP BY country
                         ORDER BY count DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CARBON[6] - Live Monitoring Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0D0D0D;
            color: #ffffff;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #0066FF 0%, #0052CC 100%);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            font-size: 36px;
            margin-bottom: 10px;
        }
        .header p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            padding: 25px;
            border-radius: 15px;
            border: 1px solid rgba(0, 102, 255, 0.2);
        }
        .stat-value {
            font-size: 48px;
            font-weight: 900;
            color: #0066FF;
            margin-bottom: 10px;
        }
        .stat-label {
            color: #888;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .stat-change {
            color: #00C853;
            font-size: 12px;
            margin-top: 5px;
        }
        .section {
            background: rgba(255, 255, 255, 0.05);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 20px;
            border: 1px solid rgba(0, 102, 255, 0.2);
        }
        .section h2 {
            color: #0066FF;
            margin-bottom: 20px;
            font-size: 24px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            text-align: left;
            padding: 12px;
            border-bottom: 2px solid rgba(0, 102, 255, 0.3);
            color: #888;
            font-size: 12px;
            text-transform: uppercase;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .status-active {
            color: #00C853;
            font-weight: 600;
        }
        .status-idle {
            color: #FFB300;
        }
        .refresh-btn {
            background: #0066FF;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            position: fixed;
            top: 20px;
            right: 20px;
        }
        .refresh-btn:hover {
            background: #0052CC;
        }
        .os-chart {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }
        .os-item {
            flex: 1;
            padding: 15px;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            text-align: center;
        }
        .os-count {
            font-size: 24px;
            font-weight: 900;
            color: #0066FF;
        }
        .pulse {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #00C853;
            border-radius: 50%;
            animation: pulse 2s infinite;
            margin-right: 8px;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
    </style>
</head>
<body>
    <button class="refresh-btn" onclick="location.reload()">↻ Refresh</button>

    <div class="header">
        <div>
            <h1>🌐 CARBON[6] Live Monitor</h1>
            <p><span class="pulse"></span>Real-time installation and usage tracking</p>
        </div>
        <a href="manage-access.php" style="background: rgba(255, 51, 102, 0.3); color: white; padding: 12px 24px; border-radius: 10px; text-decoration: none; font-weight: 600; border: 1px solid rgba(255, 51, 102, 0.5);">🔐 Manage Access</a>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value"><?= number_format($totalInstalls) ?></div>
            <div class="stat-label">Total Installations</div>
            <?php if ($recentInstalls > 0): ?>
            <div class="stat-change">+<?= $recentInstalls ?> in last 24h</div>
            <?php endif; ?>
        </div>

        <div class="stat-card">
            <div class="stat-value"><?= number_format($activeInstalls) ?></div>
            <div class="stat-label">Active Now</div>
            <div class="stat-change">Last 24 hours</div>
        </div>

        <div class="stat-card">
            <div class="stat-value"><?= count($active) ?></div>
            <div class="stat-label">Live Systems</div>
            <div class="stat-change">Last hour</div>
        </div>

        <div class="stat-card">
            <div class="stat-value"><?= $totalInstalls > 0 ? round(($activeInstalls / $totalInstalls) * 100) : 0 ?>%</div>
            <div class="stat-label">Engagement Rate</div>
            <div class="stat-change">Active / Total</div>
        </div>
    </div>

    <div class="section">
        <h2>📊 Platform Distribution</h2>
        <div class="os-chart">
            <?php foreach ($osList as $os): ?>
            <div class="os-item">
                <div class="os-count"><?= $os['count'] ?></div>
                <div><?= htmlspecialchars($os['os']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="section">
        <h2><span class="pulse"></span>Live Systems (Last Hour)</h2>
        <table>
            <thead>
                <tr>
                    <th>Install ID</th>
                    <th>Platform</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Last Seen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($active as $system): ?>
                <tr>
                    <td><code><?= substr($system['install_id'], 0, 12) ?>...</code></td>
                    <td><?= htmlspecialchars($system['os']) ?> <?= htmlspecialchars($system['os_version'] ?? '') ?></td>
                    <td><?= htmlspecialchars($system['city'] ?? 'Unknown') ?>, <?= htmlspecialchars($system['country'] ?? '') ?></td>
                    <td class="status-active">● Active</td>
                    <td><?= date('g:i A', strtotime($system['last_seen'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>🆕 Recent Installations</h2>
        <table>
            <thead>
                <tr>
                    <th>Install ID</th>
                    <th>Platform</th>
                    <th>Location</th>
                    <th>Method</th>
                    <th>Installed</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent as $install): ?>
                <tr>
                    <td><code><?= substr($install['install_id'], 0, 12) ?>...</code></td>
                    <td><?= htmlspecialchars($install['os']) ?></td>
                    <td><?= htmlspecialchars($install['city'] ?? 'Unknown') ?>, <?= htmlspecialchars($install['country'] ?? '') ?></td>
                    <td><?= htmlspecialchars($install['installation_method']) ?></td>
                    <td><?= date('M j, g:i A', strtotime($install['timestamp'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Auto-refresh every 30 seconds
        setTimeout(() => location.reload(), 30000);
    </script>
</body>
</html>

<?php
function showLogin() {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>CARBON[6] Monitor - Login</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background: #0D0D0D;
                color: #ffffff;
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100vh;
            }
            .login-box {
                background: rgba(255, 255, 255, 0.05);
                padding: 40px;
                border-radius: 15px;
                border: 1px solid rgba(0, 102, 255, 0.2);
                text-align: center;
                max-width: 400px;
            }
            h1 {
                color: #0066FF;
                margin-bottom: 30px;
            }
            input {
                width: 100%;
                padding: 15px;
                background: rgba(0, 0, 0, 0.5);
                border: 1px solid rgba(0, 102, 255, 0.3);
                border-radius: 10px;
                color: #ffffff;
                font-size: 16px;
                margin-bottom: 20px;
            }
            button {
                width: 100%;
                padding: 15px;
                background: linear-gradient(135deg, #0066FF 0%, #0052CC 100%);
                border: none;
                border-radius: 10px;
                color: white;
                font-size: 16px;
                font-weight: 700;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <div class="login-box">
            <h1>CARBON[6] Monitor</h1>
            <form method="POST">
                <input type="password" name="password" placeholder="Dashboard Password" required autofocus>
                <button type="submit">Access Dashboard</button>
            </form>
        </div>
    </body>
    </html>
    <?php
}
?>
