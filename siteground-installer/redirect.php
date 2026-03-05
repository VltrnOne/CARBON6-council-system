<?php
/**
 * CARBON[6] Install Code Redirect Handler
 * Handles: thecarbon6.agency/install/XXXXXXXX
 */
require_once 'config.php';

// Get code from URL
$requestUri = $_SERVER['REQUEST_URI'];
preg_match('/\/install\/([A-Z0-9]+)/', $requestUri, $matches);

if (!isset($matches[1])) {
    showError('Invalid URL', 'No installation code provided.');
    exit;
}

$code = $matches[1];

// Initialize database
$db = initDatabase();
cleanExpiredCodes();

// Get code details
$stmt = $db->prepare("SELECT * FROM install_codes WHERE code = ?");
$stmt->execute([$code]);
$codeData = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if code exists
if (!$codeData) {
    showError('Invalid or Expired Code', 'This installation code is invalid or has expired.');
    exit;
}

// Check if already used
if ($codeData['used']) {
    showError('Code Already Used', 'This one-time installation code has already been used on ' .
              date('M j, Y \a\t g:i A', strtotime($codeData['used_at'])));
    exit;
}

// Mark as used
$stmt = $db->prepare("UPDATE install_codes SET used = 1, used_at = datetime('now'), user_ip = ?, user_agent = ? WHERE code = ?");
$stmt->execute([
    $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
    $code
]);

// Redirect to installer
header('Location: ' . INSTALLER_URL);
exit;

function showError($title, $message) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($title); ?> - CARBON[6]</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background: #0D0D0D;
                color: #ffffff;
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100vh;
                margin: 0;
                text-align: center;
            }
            .error-container {
                max-width: 500px;
                padding: 40px;
            }
            .logo {
                font-size: 72px;
                font-weight: 900;
                margin-bottom: 20px;
                color: #0066FF;
            }
            h1 {
                font-size: 24px;
                margin-bottom: 15px;
                color: #0066FF;
            }
            p {
                font-size: 16px;
                color: #888;
                line-height: 1.6;
            }
            .back-link {
                display: inline-block;
                margin-top: 30px;
                padding: 12px 30px;
                background: rgba(0, 102, 255, 0.2);
                border: 1px solid #0066FF;
                border-radius: 8px;
                color: #0066FF;
                text-decoration: none;
                transition: all 0.3s ease;
            }
            .back-link:hover {
                background: rgba(0, 102, 255, 0.3);
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <div class="logo">C[6]</div>
            <h1><?php echo htmlspecialchars($title); ?></h1>
            <p><?php echo htmlspecialchars($message); ?></p>
            <a href="https://thecarbon6.agency" class="back-link">Return to Homepage</a>
        </div>
    </body>
    </html>
    <?php
}
