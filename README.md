# CARBON[6] Council System - Complete Distribution Suite

**Production-ready installer with one-time code security system**

---

## 🎯 WHAT IS THIS?

Complete automated deployment system for the CARBON[6] Council System:

1. **Beautiful Installer** - GitHub Pages hosted, auto-detects OS
2. **One-Time Codes** - Secure, expiring access codes
3. **Full Automation** - CLI tools for everything
4. **REST API** - Programmatic access
5. **Monitoring** - 24/7 uptime tracking

---

## ⚡ QUICK START (30 SECONDS)

```bash
cd /Users/Morpheous/sovereign-manifold-distribution
./carbon6-install-cli.sh
```

**Select option 1** → Enter FTP credentials → **Done!**

---

## 📦 COMPLETE SYSTEM

### 🎮 Master Control
- **carbon6-install-cli.sh** - Unified CLI interface
- One command to access everything

### 🚀 Deployment Tools
- **deploy-to-siteground.sh** - Automated FTP deployment
- **test-deployment.sh** - Verification testing
- **carbon6-install-system.tar.gz** - Complete package

### 🔧 Management Tools
- **manage-codes.sh** - CLI code manager
- **monitor-system.sh** - System monitor

### 🌐 Public Installer
- **index.html** - GitHub Pages installer
- Auto-detects OS, copies command
- Live: https://vltrnone.github.io/CARBON6-council-system/

### 🔒 One-Time Code System
- **siteground-installer/** - PHP-based code generator
- Admin: https://thecarbon6.agency/install
- Generate codes: K7QW3M9P
- Each works once, auto-expires

### 🔌 API Access
- **REST API** - Programmatic code generation
- **Node.js Client** - carbon6-install-api-client.js
- **Python Client** - carbon6_install_api.py
- **Full Docs** - API-DOCUMENTATION.md

---

## 🎯 THREE WAYS TO USE

### 1. Public Installer (Anyone)
```
https://vltrnone.github.io/CARBON6-council-system/
```
- Click "Install Now"
- Command auto-copied
- Paste in terminal

### 2. One-Time Codes (Controlled)
```
https://thecarbon6.agency/install/K7QW3M9P
```
- Generate unique codes
- Share with specific users
- Works once, expires automatically

### 3. API Integration (Automated)
```javascript
const api = new Carbon6InstallAPI('password');
const result = await api.generateCode();
```
- Programmatic access
- Automate onboarding
- Integrate with systems

---

## 🚀 DEPLOYMENT

### Option 1: Master CLI (Recommended)
```bash
./carbon6-install-cli.sh
```
Beautiful menu with all options.

### Option 2: Auto-Deploy Script
```bash
./deploy-to-siteground.sh
```
Direct FTP upload.

### Option 3: Manual Upload
Upload `siteground-installer/` to `/public_html/install/`

---

## 📊 FEATURES

✅ **One-Time Use Codes** - Each code works once
✅ **Auto-Expiration** - 1 hour to 30 days
✅ **Beautiful UI** - CARBON[6] branded
✅ **REST API** - Programmatic access
✅ **CLI Tools** - Complete automation
✅ **24/7 Monitoring** - Uptime tracking
✅ **Multi-Language** - Node.js, Python, cURL
✅ **SiteGround Optimized** - No additional services needed
✅ **GitHub Pages** - Public installer

---

## 🎮 MASTER CLI MENU

```
═══ DEPLOYMENT ═══
  1. Deploy to SiteGround
  2. Test Deployment
  3. Update Files

═══ CODE MANAGEMENT ═══
  4. Generate New Code
  5. Check Code Status
  6. View Analytics

═══ MONITORING ═══
  7. System Status
  8. Start Monitor
  9. View Logs

═══ UTILITIES ═══
  10. Backup Database
  11. Documentation
  12. Exit
```

---

## 📚 DOCUMENTATION

- **README.md** - This file (overview)
- **API-DOCUMENTATION.md** - Complete API reference
- **siteground-installer/SETUP.md** - Deployment guide
- **siteground-installer/README.md** - Quick start

---

## 🔗 LIVE URLS

**Public Installer:**
- https://vltrnone.github.io/CARBON6-council-system/

**Admin Panel:**
- https://thecarbon6.agency/install

**Generated Codes:**
- https://thecarbon6.agency/install/XXXXXXXX

**API Base:**
- https://thecarbon6.agency/install/api

---

## 🛠️ TECH STACK

**Frontend:**
- HTML5, CSS3, JavaScript
- GitHub Pages hosting
- OS auto-detection

**Backend:**
- PHP 7.4+
- SQLite database
- SiteGround hosting

**API:**
- RESTful architecture
- Bearer token auth
- JSON responses

**CLI:**
- Bash scripts
- Automated deployment
- Background monitoring

---

## 🔒 SECURITY

✅ Password-protected admin
✅ One-time use enforcement
✅ Automatic expiration
✅ HTTPS-only access
✅ Database protection (.htaccess)
✅ API authentication
✅ No exposed code links

---

## 📊 USAGE EXAMPLES

### Generate Code via CLI
```bash
./manage-codes.sh
# Select: Generate New Code
```

### Generate Code via API (Node.js)
```javascript
const api = new Carbon6InstallAPI('password');
const result = await api.generateCode({
  purpose: 'Client Demo',
  expiry: 1
});
console.log(result.url);
```

### Generate Code via API (Python)
```python
api = Carbon6InstallAPI('password')
result = api.generate_code(purpose='Client Demo', expiry=1)
print(result['url'])
```

### Monitor System
```bash
./monitor-system.sh
# Runs continuously, checks every 5 minutes
```

---

## 💰 COST

**FREE** - Everything included:
- SiteGround hosting (your existing plan)
- GitHub Pages hosting (free)
- No additional services
- No monthly fees
- Unlimited codes

---

## 🎯 STATUS

✅ **Installer:** Live on GitHub Pages
✅ **Code System:** Ready to deploy
✅ **API:** Complete with clients
✅ **CLI Tools:** Full automation
✅ **Monitoring:** Background service
✅ **Documentation:** Comprehensive

---

## 🚀 NEXT STEPS

1. **Deploy:** `./carbon6-install-cli.sh` → Option 1
2. **Test:** Option 2 after deployment
3. **Generate:** Option 4 to create first code
4. **Monitor:** Option 8 for 24/7 tracking

---

## 📞 SUPPORT

- **Documentation:** This repository
- **GitHub:** https://github.com/VltrnOne/CARBON6-council-system
- **Issues:** GitHub Issues

---

## 🏆 WHAT MAKES THIS UNIQUE

**Most installers:**
- Public URL anyone can access
- No usage tracking
- No expiration
- Basic or no UI

**CARBON[6] System:**
- ✅ One-time use codes
- ✅ Automatic expiration
- ✅ Beautiful branded UI
- ✅ Full REST API
- ✅ Complete automation
- ✅ 24/7 monitoring
- ✅ Multi-language clients
- ✅ Purpose tracking
- ✅ Analytics dashboard

---

**Build. Own. Govern.**

*Infrastructure that compounds. Everywhere.*

---

**Version:** 1.0.0
**Released:** March 2026
**Status:** Production Ready
**License:** MIT
