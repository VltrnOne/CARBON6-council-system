<?php
/**
 * CARBON[6] Access Control API
 * Manage installation access (suspend, unsuspend, revoke)
 */

session_start();

// Authentication check
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$db = new PDO('sqlite:' . __DIR__ . '/carbon_telemetry.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Add status column if it doesn't exist
try {
    $db->exec("ALTER TABLE installations ADD COLUMN status TEXT DEFAULT 'active'");
} catch (PDOException $e) {
    // Column already exists
}

// Add revoked_at and revoked_reason columns
try {
    $db->exec("ALTER TABLE installations ADD COLUMN revoked_at DATETIME");
    $db->exec("ALTER TABLE installations ADD COLUMN revoked_reason TEXT");
} catch (PDOException $e) {
    // Columns already exist
}

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

if ($method === 'POST') {
    $action = $data['action'] ?? '';
    $installId = $data['install_id'] ?? '';

    if (empty($installId)) {
        http_response_code(400);
        echo json_encode(['error' => 'install_id required']);
        exit;
    }

    switch ($action) {
        case 'suspend':
            $stmt = $db->prepare("UPDATE installations SET status = 'suspended' WHERE install_id = ?");
            $stmt->execute([$installId]);

            // Log the event
            logAccessEvent($db, $installId, 'suspended', $data['reason'] ?? 'Manual suspension');

            echo json_encode([
                'success' => true,
                'message' => 'Installation suspended',
                'install_id' => $installId
            ]);
            break;

        case 'unsuspend':
            $stmt = $db->prepare("UPDATE installations SET status = 'active' WHERE install_id = ?");
            $stmt->execute([$installId]);

            logAccessEvent($db, $installId, 'unsuspended', 'Access restored');

            echo json_encode([
                'success' => true,
                'message' => 'Installation reactivated',
                'install_id' => $installId
            ]);
            break;

        case 'revoke':
            $reason = $data['reason'] ?? 'Access revoked by administrator';
            $stmt = $db->prepare("UPDATE installations
                                  SET status = 'revoked',
                                      revoked_at = datetime('now'),
                                      revoked_reason = ?
                                  WHERE install_id = ?");
            $stmt->execute([$reason, $installId]);

            logAccessEvent($db, $installId, 'revoked', $reason);

            echo json_encode([
                'success' => true,
                'message' => 'Installation permanently revoked',
                'install_id' => $installId
            ]);
            break;

        case 'bulk_suspend':
            $installIds = $data['install_ids'] ?? [];
            if (empty($installIds)) {
                http_response_code(400);
                echo json_encode(['error' => 'install_ids array required']);
                exit;
            }

            $placeholders = str_repeat('?,', count($installIds) - 1) . '?';
            $stmt = $db->prepare("UPDATE installations SET status = 'suspended' WHERE install_id IN ($placeholders)");
            $stmt->execute($installIds);

            foreach ($installIds as $id) {
                logAccessEvent($db, $id, 'suspended', 'Bulk suspension');
            }

            echo json_encode([
                'success' => true,
                'message' => count($installIds) . ' installations suspended',
                'count' => count($installIds)
            ]);
            break;

        case 'list':
            $status = $data['status'] ?? 'all';
            $query = "SELECT i.*,
                      (SELECT COUNT(*) FROM heartbeats h WHERE h.install_id = i.install_id) as heartbeat_count,
                      (SELECT h.timestamp FROM heartbeats h WHERE h.install_id = i.install_id ORDER BY timestamp DESC LIMIT 1) as last_seen
                      FROM installations i";

            if ($status !== 'all') {
                $query .= " WHERE i.status = :status";
            }

            $query .= " ORDER BY i.timestamp DESC";

            $stmt = $db->prepare($query);
            if ($status !== 'all') {
                $stmt->bindParam(':status', $status);
            }
            $stmt->execute();

            echo json_encode([
                'success' => true,
                'installations' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}

function logAccessEvent($db, $installId, $eventType, $reason) {
    $stmt = $db->prepare("INSERT INTO events (install_id, event_type, event_data) VALUES (?, ?, ?)");
    $stmt->execute([$installId, 'access_control_' . $eventType, json_encode(['reason' => $reason])]);
}
