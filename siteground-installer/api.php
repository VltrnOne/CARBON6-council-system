<?php
/**
 * CARBON[6] Install Code System - REST API
 * Programmatic access to code generation and management
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'config.php';

// API Authentication
$headers = getallheaders();
$apiKey = $headers['Authorization'] ?? $headers['authorization'] ?? '';

if ($apiKey !== 'Bearer ' . ADMIN_PASSWORD) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['PATH_INFO'] ?? '/';

// Initialize database
$db = initDatabase();
cleanExpiredCodes();

// Route requests
if ($method === 'POST' && $path === '/generate') {
    // Generate new code
    $data = json_decode(file_get_contents('php://input'), true);

    $code = generateCode();
    $expiry = $data['expiry'] ?? DEFAULT_EXPIRY_HOURS;
    $purpose = $data['purpose'] ?? 'API Generated';

    $expiryDate = date('Y-m-d H:i:s', strtotime("+{$expiry} hours"));

    $stmt = $db->prepare("INSERT INTO install_codes (code, expires_at, purpose) VALUES (?, ?, ?)");
    $stmt->execute([$code, $expiryDate, $purpose]);

    echo json_encode([
        'success' => true,
        'code' => $code,
        'expires' => $expiryDate,
        'url' => 'https://thecarbon6.agency/install/' . $code
    ]);

} elseif ($method === 'GET' && preg_match('/\/check\/([A-Z0-9]+)/', $path, $matches)) {
    // Check code status
    $code = $matches[1];

    $stmt = $db->prepare("SELECT * FROM install_codes WHERE code = ?");
    $stmt->execute([$code]);
    $codeData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$codeData) {
        echo json_encode([
            'success' => false,
            'status' => 'invalid',
            'message' => 'Code not found or expired'
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'status' => $codeData['used'] ? 'used' : 'active',
            'code' => $code,
            'created' => $codeData['created_at'],
            'expires' => $codeData['expires_at'],
            'purpose' => $codeData['purpose'],
            'used_at' => $codeData['used_at']
        ]);
    }

} elseif ($method === 'GET' && $path === '/stats') {
    // Get statistics
    $total = $db->query("SELECT COUNT(*) FROM install_codes")->fetchColumn();
    $active = $db->query("SELECT COUNT(*) FROM install_codes WHERE used = 0 AND expires_at > datetime('now')")->fetchColumn();
    $used = $db->query("SELECT COUNT(*) FROM install_codes WHERE used = 1")->fetchColumn();
    $expired = $db->query("SELECT COUNT(*) FROM install_codes WHERE expires_at <= datetime('now') AND used = 0")->fetchColumn();

    echo json_encode([
        'success' => true,
        'stats' => [
            'total_generated' => $total,
            'active' => $active,
            'used' => $used,
            'expired' => $expired
        ]
    ]);

} elseif ($method === 'GET' && $path === '/list') {
    // List recent codes
    $limit = $_GET['limit'] ?? 20;

    $stmt = $db->prepare("SELECT code, created_at, expires_at, purpose, used, used_at
                          FROM install_codes
                          ORDER BY created_at DESC
                          LIMIT ?");
    $stmt->execute([$limit]);
    $codes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'codes' => $codes
    ]);

} else {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint not found']);
}
