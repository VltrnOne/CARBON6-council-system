# CARBON[6] Install Code System - API Documentation

**REST API for programmatic code generation and management**

---

## 🔑 Authentication

All API requests require authentication via Bearer token:

```
Authorization: Bearer YOUR_ADMIN_PASSWORD
```

**Base URL:** `https://thecarbon6.agency/install/api`

---

## 📋 Endpoints

### 1. Generate Code

**POST** `/generate`

Generate a new one-time use install code.

**Request:**
```json
{
  "purpose": "Client Demo",
  "expiry": 24
}
```

**Parameters:**
- `purpose` (string, optional): Label for tracking (default: "Generated")
- `expiry` (integer, optional): Hours until expiry, 1-720 (default: 24)

**Response:**
```json
{
  "success": true,
  "code": "K7QW3M9P",
  "expires": "2026-03-06 12:00:00",
  "url": "https://thecarbon6.agency/install/K7QW3M9P"
}
```

**Example:**
```bash
curl -X POST https://thecarbon6.agency/install/api/generate \
  -H "Authorization: Bearer YOUR_PASSWORD" \
  -H "Content-Type: application/json" \
  -d '{"purpose":"Client Demo","expiry":1}'
```

---

### 2. Check Code Status

**GET** `/check/{code}`

Check if a code is valid, used, or expired.

**Response:**
```json
{
  "success": true,
  "status": "active",
  "code": "K7QW3M9P",
  "created": "2026-03-05 12:00:00",
  "expires": "2026-03-06 12:00:00",
  "purpose": "Client Demo",
  "used_at": null
}
```

**Status values:**
- `active` - Code is valid and unused
- `used` - Code has been redeemed
- `invalid` - Code not found or expired

**Example:**
```bash
curl https://thecarbon6.agency/install/api/check/K7QW3M9P \
  -H "Authorization: Bearer YOUR_PASSWORD"
```

---

### 3. Get Statistics

**GET** `/stats`

Get system-wide statistics.

**Response:**
```json
{
  "success": true,
  "stats": {
    "total_generated": 150,
    "active": 25,
    "used": 100,
    "expired": 25
  }
}
```

**Example:**
```bash
curl https://thecarbon6.agency/install/api/stats \
  -H "Authorization: Bearer YOUR_PASSWORD"
```

---

### 4. List Codes

**GET** `/list?limit=20`

List recent codes (newest first).

**Parameters:**
- `limit` (integer, optional): Number of results (default: 20, max: 100)

**Response:**
```json
{
  "success": true,
  "codes": [
    {
      "code": "K7QW3M9P",
      "created_at": "2026-03-05 12:00:00",
      "expires_at": "2026-03-06 12:00:00",
      "purpose": "Client Demo",
      "used": 0,
      "used_at": null
    }
  ]
}
```

**Example:**
```bash
curl https://thecarbon6.agency/install/api/list?limit=10 \
  -H "Authorization: Bearer YOUR_PASSWORD"
```

---

## 🔒 Error Responses

### 401 Unauthorized
```json
{
  "error": "Unauthorized"
}
```
**Cause:** Invalid or missing API key

### 404 Not Found
```json
{
  "error": "Endpoint not found"
}
```
**Cause:** Invalid endpoint

### 500 Server Error
```json
{
  "error": "Internal server error"
}
```
**Cause:** Server-side issue

---

## 📚 Client Libraries

### Node.js

```javascript
const Carbon6InstallAPI = require('./carbon6-install-api-client');

const api = new Carbon6InstallAPI('your-password');

// Generate code
const result = await api.generateCode({
  purpose: 'Client Demo',
  expiry: 1
});

console.log(result.url);
```

### Python

```python
from carbon6_install_api import Carbon6InstallAPI

api = Carbon6InstallAPI('your-password')

# Generate code
result = api.generate_code(purpose='Client Demo', expiry=1)
print(result['url'])
```

### cURL

```bash
# Generate code
curl -X POST https://thecarbon6.agency/install/api/generate \
  -H "Authorization: Bearer YOUR_PASSWORD" \
  -H "Content-Type: application/json" \
  -d '{"purpose":"Test","expiry":1}'

# Check code
curl https://thecarbon6.agency/install/api/check/K7QW3M9P \
  -H "Authorization: Bearer YOUR_PASSWORD"

# Get stats
curl https://thecarbon6.agency/install/api/stats \
  -H "Authorization: Bearer YOUR_PASSWORD"
```

---

## 🎯 Use Cases

### Automated Client Onboarding

```javascript
// Generate unique code for each new client
async function onboardClient(clientName) {
  const api = new Carbon6InstallAPI(process.env.CARBON6_API_KEY);

  const result = await api.generateCode({
    purpose: `Client: ${clientName}`,
    expiry: 168  // 7 days
  });

  // Email code to client
  await sendEmail(clientName, result.url);

  return result.code;
}
```

### Demo System Integration

```python
# Generate temporary codes for demos
def create_demo_code():
    api = Carbon6InstallAPI(os.getenv('CARBON6_API_KEY'))

    result = api.generate_code(
        purpose='Product Demo',
        expiry=1  # 1 hour
    )

    return result['url']
```

### Analytics Dashboard

```javascript
// Track code usage
async function getUsageMetrics() {
  const api = new Carbon6InstallAPI(process.env.CARBON6_API_KEY);

  const stats = await api.getStats();
  const codes = await api.listCodes(50);

  return {
    conversionRate: stats.stats.used / stats.stats.total_generated,
    recentActivity: codes
  };
}
```

---

## 🔐 Security Best Practices

1. **Store API key securely**
   - Use environment variables
   - Never commit to version control
   - Rotate regularly

2. **Use HTTPS only**
   - All API calls are HTTPS-only
   - Certificate verified automatically

3. **Rate limiting**
   - Implement client-side rate limiting
   - Cache stats/list results

4. **Monitor usage**
   - Track API calls
   - Alert on suspicious patterns

---

## 🧪 Testing

```bash
# Test authentication
curl -i https://thecarbon6.agency/install/api/stats \
  -H "Authorization: Bearer test-invalid-key"
# Expected: 401 Unauthorized

# Test generation
curl -X POST https://thecarbon6.agency/install/api/generate \
  -H "Authorization: Bearer YOUR_PASSWORD" \
  -H "Content-Type: application/json" \
  -d '{"purpose":"Test","expiry":1}'
# Expected: 200 OK with code
```

---

## 📊 Rate Limits

**Current:** No rate limits enforced
**Recommended:** 100 requests/hour for automated systems

---

## 🆘 Support

- **Documentation:** This file
- **GitHub:** https://github.com/VltrnOne/CARBON6-council-system
- **Issues:** Report bugs via GitHub Issues

---

**API Version:** 1.0.0
**Last Updated:** March 5, 2026
