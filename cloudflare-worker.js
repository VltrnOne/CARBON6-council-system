/**
 * CARBON[6] Council System - One-Time Install Code Generator
 * Deploy to: thecarbon6.agency/install
 *
 * Features:
 * - Generate unique one-time use codes
 * - Track code usage
 * - Automatic expiration
 * - Admin dashboard
 */

// Configuration
const CONFIG = {
  INSTALLER_URL: 'https://vltrnone.github.io/CARBON6-council-system/',
  CODE_LENGTH: 8,
  CODE_EXPIRY_HOURS: 24,
  ADMIN_PASSWORD: 'CHANGE_THIS_PASSWORD', // ⚠️ Change this!
};

export default {
  async fetch(request, env) {
    const url = new URL(request.url);
    const path = url.pathname;

    // CORS headers
    const corsHeaders = {
      'Access-Control-Allow-Origin': '*',
      'Access-Control-Allow-Methods': 'GET, POST, OPTIONS',
      'Access-Control-Allow-Headers': 'Content-Type, Authorization',
    };

    if (request.method === 'OPTIONS') {
      return new Response(null, { headers: corsHeaders });
    }

    // Routes
    if (path === '/install' || path === '/install/') {
      return handleAdmin(request);
    } else if (path.startsWith('/install/') && path.length > 9) {
      const code = path.replace('/install/', '');
      return handleRedirect(code, env);
    } else if (path === '/api/generate') {
      return handleGenerate(request, env);
    } else if (path === '/api/stats') {
      return handleStats(request, env);
    }

    return new Response('Not Found', { status: 404 });
  }
};

// Admin Dashboard HTML
function handleAdmin(request) {
  const html = `<!DOCTYPE html>
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
        .stats {
            margin-top: 30px;
            padding: 20px;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            font-size: 14px;
            color: #666;
        }
        .stat-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .stat-row:last-child { border-bottom: none; }
        .stat-label { color: #888; }
        .stat-value { color: #0066FF; font-weight: 600; }
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
            <button class="copy-btn" onclick="copyCode()">Copy Full URL</button>
            <button class="copy-btn" onclick="copyCodeOnly()">Copy Code Only</button>
        </div>

        <div class="stats">
            <div class="stat-row">
                <span class="stat-label">Status</span>
                <span class="stat-value">Ready</span>
            </div>
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
                const response = await fetch('/api/generate', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ password, purpose, expiry })
                });

                if (!response.ok) {
                    throw new Error('Invalid password or server error');
                }

                const data = await response.json();
                const fullUrl = window.location.origin + '/install/' + data.code;

                document.getElementById('code').textContent = data.code;
                document.getElementById('url').textContent = fullUrl;
                document.getElementById('output').classList.add('active');
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        function copyCode() {
            const url = document.getElementById('url').textContent;
            navigator.clipboard.writeText(url).then(() => {
                alert('Full URL copied to clipboard!');
            });
        }

        function copyCodeOnly() {
            const code = document.getElementById('code').textContent;
            navigator.clipboard.writeText(code).then(() => {
                alert('Code copied to clipboard!');
            });
        }
    </script>
</body>
</html>`;

  return new Response(html, {
    headers: { 'Content-Type': 'text/html' }
  });
}

// Generate new one-time code
async function handleGenerate(request, env) {
  try {
    const body = await request.json();

    // Verify admin password
    if (body.password !== CONFIG.ADMIN_PASSWORD) {
      return new Response(JSON.stringify({ error: 'Invalid password' }), {
        status: 401,
        headers: { 'Content-Type': 'application/json' }
      });
    }

    // Generate unique code
    const code = generateUniqueCode();
    const expiry = parseInt(body.expiry) || 24;
    const expiryDate = new Date();
    expiryDate.setHours(expiryDate.getHours() + expiry);

    // Store in KV
    const metadata = {
      code: code,
      created: new Date().toISOString(),
      expires: expiryDate.toISOString(),
      purpose: body.purpose || 'General',
      used: false,
      usedAt: null,
      userAgent: null,
      ip: null
    };

    await env.INSTALL_CODES.put(code, JSON.stringify(metadata), {
      expirationTtl: expiry * 3600 // Convert hours to seconds
    });

    return new Response(JSON.stringify({
      code: code,
      expires: expiryDate.toISOString(),
      url: `/install/${code}`
    }), {
      headers: { 'Content-Type': 'application/json' }
    });

  } catch (error) {
    return new Response(JSON.stringify({ error: error.message }), {
      status: 500,
      headers: { 'Content-Type': 'application/json' }
    });
  }
}

// Handle redirect with one-time code
async function handleRedirect(code, env) {
  try {
    // Get code metadata
    const metadataStr = await env.INSTALL_CODES.get(code);

    if (!metadataStr) {
      return new Response(errorPage('Invalid or Expired Code', 'This installation code is invalid or has expired.'), {
        status: 404,
        headers: { 'Content-Type': 'text/html' }
      });
    }

    const metadata = JSON.parse(metadataStr);

    // Check if already used
    if (metadata.used) {
      return new Response(errorPage('Code Already Used', 'This one-time installation code has already been used.'), {
        status: 403,
        headers: { 'Content-Type': 'text/html' }
      });
    }

    // Mark as used
    metadata.used = true;
    metadata.usedAt = new Date().toISOString();

    await env.INSTALL_CODES.put(code, JSON.stringify(metadata), {
      expirationTtl: 86400 // Keep used codes for 24 hours for tracking
    });

    // Redirect to installer
    return Response.redirect(CONFIG.INSTALLER_URL, 302);

  } catch (error) {
    return new Response(errorPage('Server Error', 'An error occurred. Please contact support.'), {
      status: 500,
      headers: { 'Content-Type': 'text/html' }
    });
  }
}

// Generate unique code
function generateUniqueCode() {
  const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // Removed confusing chars (0,O,1,I)
  let code = '';
  for (let i = 0; i < CONFIG.CODE_LENGTH; i++) {
    code += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  return code;
}

// Error page HTML
function errorPage(title, message) {
  return `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>${title} - CARBON[6]</title>
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
    </style>
</head>
<body>
    <div class="error-container">
        <div class="logo">C[6]</div>
        <h1>${title}</h1>
        <p>${message}</p>
    </div>
</body>
</html>`;
}

// Stats endpoint
async function handleStats(request, env) {
  // TODO: Implement stats tracking
  return new Response(JSON.stringify({ stats: 'coming soon' }), {
    headers: { 'Content-Type': 'application/json' }
  });
}
