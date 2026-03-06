<?php
/**
 * CARBON[6] Telemetry Collection System
 * Real-time monitoring of installations and active users
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Database setup
$dbFile = __DIR__ . '/carbon_telemetry.db';
$db = new PDO('sqlite:' . $dbFile);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create tables if not exist
$db->exec("CREATE TABLE IF NOT EXISTS installations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    install_id TEXT UNIQUE NOT NULL,
    install_code TEXT,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    os TEXT,
    os_version TEXT,
    architecture TEXT,
    ip_address TEXT,
    country TEXT,
    region TEXT,
    city TEXT,
    user_agent TEXT,
    installation_method TEXT,
    system_info TEXT
)");

$db->exec("CREATE TABLE IF NOT EXISTS heartbeats (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    install_id TEXT NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    status TEXT DEFAULT 'active',
    uptime_seconds INTEGER,
    cpu_usage REAL,
    memory_usage REAL,
    active_agents INTEGER,
    system_version TEXT,
    FOREIGN KEY (install_id) REFERENCES installations(install_id)
)");

$db->exec("CREATE TABLE IF NOT EXISTS events (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    install_id TEXT NOT NULL,
    event_type TEXT NOT NULL,
    event_data TEXT,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (install_id) REFERENCES installations(install_id)
)");

$db->exec("CREATE INDEX IF NOT EXISTS idx_install_id ON heartbeats(install_id)");
$db->exec("CREATE INDEX IF NOT EXISTS idx_timestamp ON heartbeats(timestamp)");

// Add status column for access control
try {
    $db->exec("ALTER TABLE installations ADD COLUMN status TEXT DEFAULT 'active'");
} catch (PDOException $e) {
    // Column already exists
}

// Handle requests
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';

    switch ($action) {
        case 'install':
            handleInstall($db, $data);
            break;

        case 'heartbeat':
            handleHeartbeat($db, $data);
            break;

        case 'event':
            handleEvent($db, $data);
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}

function handleInstall($db, $data) {
    // Get IP info
    $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $ipInfo = getIpInfo($ip);

    $stmt = $db->prepare("INSERT OR REPLACE INTO installations
        (install_id, install_code, os, os_version, architecture, ip_address,
         country, region, city, user_agent, installation_method, system_info)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $data['install_id'],
        $data['install_code'] ?? null,
        $data['os'] ?? 'unknown',
        $data['os_version'] ?? null,
        $data['architecture'] ?? null,
        $ip,
        $ipInfo['country'] ?? null,
        $ipInfo['region'] ?? null,
        $ipInfo['city'] ?? null,
        $_SERVER['HTTP_USER_AGENT'] ?? null,
        $data['method'] ?? 'manual',
        json_encode($data['system_info'] ?? [])
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Installation recorded',
        'install_id' => $data['install_id']
    ]);
}

function handleHeartbeat($db, $data) {
    // Check if installation is allowed
    if (!checkInstallationAccess($db, $data['install_id'])) {
        http_response_code(403);
        echo json_encode(['error' => 'Access suspended or revoked']);
        exit;
    }

    $stmt = $db->prepare("INSERT INTO heartbeats
        (install_id, status, uptime_seconds, cpu_usage, memory_usage, active_agents, system_version)
        VALUES (?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $data['install_id'],
        $data['status'] ?? 'active',
        $data['uptime'] ?? 0,
        $data['cpu_usage'] ?? null,
        $data['memory_usage'] ?? null,
        $data['active_agents'] ?? null,
        $data['version'] ?? null
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Heartbeat recorded'
    ]);
}

function handleEvent($db, $data) {
    $stmt = $db->prepare("INSERT INTO events
        (install_id, event_type, event_data)
        VALUES (?, ?, ?)");

    $stmt->execute([
        $data['install_id'],
        $data['event_type'],
        json_encode($data['event_data'] ?? [])
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Event recorded'
    ]);
}

function getIpInfo($ip) {
    // Simple IP geolocation (optional - can integrate with ipinfo.io, ipapi.co, etc.)
    // For now, return empty - can be enhanced with API calls
    return [
        'country' => null,
        'region' => null,
        'city' => null
    ];
}

function checkInstallationAccess($db, $installId) {
    // Check if installation exists and is active
    $stmt = $db->prepare("SELECT status FROM installations WHERE install_id = ?");
    $stmt->execute([$installId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        // Installation not found - allow (will be created)
        return true;
    }

    // Only allow if status is 'active' or null (legacy)
    return ($result['status'] === 'active' || $result['status'] === null);
}
