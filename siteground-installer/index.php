<?php
/**
 * CARBON[6] Install Code Generator - Admin Interface
 */
require_once 'config.php';

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents('php://input'), true);

    // Verify password
    if ($data['password'] !== ADMIN_PASSWORD) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid password']);
        exit;
    }

    // Generate code
    $db = initDatabase();
    $code = generateCode();
    $expiry = $data['expiry'] ?? DEFAULT_EXPIRY_HOURS;
    $purpose = $data['purpose'] ?? 'General';

    $expiryDate = date('Y-m-d H:i:s', strtotime("+{$expiry} hours"));

    $stmt = $db->prepare("INSERT INTO install_codes (code, expires_at, purpose) VALUES (?, ?, ?)");
    $stmt->execute([$code, $expiryDate, $purpose]);

    echo json_encode([
        'code' => $code,
        'expires' => $expiryDate,
        'url' => 'https://thecarbon6.agency/install/' . $code
    ]);
    exit;
}

// Clean old codes
cleanExpiredCodes();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CARBON[6] Install Code Generator</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 40px;
            border: 1px solid rgba(0, 102, 255, 0.2);
        }
        .logo {
            font-size: 48px;
            font-weight: 900;
            text-align: center;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #0066FF 0%, #448AFF 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 40px;
            color: #888;
        }
        .input-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #888;
            font-size: 14px;
        }
        input, select {
            width: 100%;
            padding: 15px;
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(0, 102, 255, 0.3);
            border-radius: 10px;
            color: #ffffff;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #0066FF 0%, #0052CC 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 102, 255, 0.4);
        }
        .code-output {
            display: none;
            margin-top: 30px;
            padding: 25px;
            background: rgba(0, 102, 255, 0.1);
            border: 2px solid #0066FF;
            border-radius: 15px;
        }
        .code-output.active { display: block; }
        .code-text {
            font-size: 28px;
            font-weight: 900;
            color: #0066FF;
            text-align: center;
            font-family: 'Courier New', monospace;
            margin-bottom: 15px;
            word-break: break-all;
        }
        .code-url {
            font-size: 14px;
            color: #888;
            text-align: center;
            word-break: break-all;
            margin-bottom: 15px;
        }
        .copy-btn {
            width: 100%;
            padding: 12px;
            background: rgba(0, 102, 255, 0.2);
            border: 1px solid #0066FF;
            color: #0066FF;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 10px;
        }
        .copy-btn:hover { background: rgba(0, 102, 255, 0.3); }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">C[6]</div>
        <h1>Install Code Generator</h1>

        <div class="input-group">
            <label>Admin Password</label>
            <input type="password" id="password" placeholder="Enter admin password">
        </div>

        <div class="input-group">
            <label>Code Purpose (optional)</label>
            <input type="text" id="purpose" placeholder="e.g., Client Demo, Partner Share">
        </div>

        <div class="input-group">
            <label>Expiry Time</label>
            <select id="expiry">
                <option value="1">1 hour</option>
                <option value="24" selected>24 hours</option>
                <option value="168">7 days</option>
                <option value="720">30 days</option>
            </select>
        </div>

        <button onclick="generateCode()">Generate One-Time Code</button>

        <div id="output" class="code-output">
            <div class="code-text" id="code"></div>
            <div class="code-url" id="url"></div>
            <button class="copy-btn" onclick="copyUrl()">Copy Full URL</button>
            <button class="copy-btn" onclick="copyCode()">Copy Code Only</button>
        </div>
    </div>

    <script>
        async function generateCode() {
            const password = document.getElementById('password').value;
            const purpose = document.getElementById('purpose').value || 'General';
            const expiry = document.getElementById('expiry').value;

            if (!password) {
                alert('Please enter admin password');
                return;
            }

            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ password, purpose, expiry })
                });

                if (!response.ok) {
                    throw new Error('Invalid password or server error');
                }

                const data = await response.json();

                document.getElementById('code').textContent = data.code;
                document.getElementById('url').textContent = data.url;
                document.getElementById('output').classList.add('active');
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        function copyUrl() {
            const url = document.getElementById('url').textContent;
            navigator.clipboard.writeText(url).then(() => {
                alert('Full URL copied to clipboard!');
            });
        }

        function copyCode() {
            const code = document.getElementById('code').textContent;
            navigator.clipboard.writeText(code).then(() => {
                alert('Code copied to clipboard!');
            });
        }
    </script>
</body>
</html>
