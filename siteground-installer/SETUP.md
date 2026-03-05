# CARBON[6] One-Time Install Codes - SiteGround Setup

**Simple PHP system for your existing SiteGround hosting**

---

## 🚀 QUICK SETUP (3 Minutes)

### Step 1: Upload Files

**Via cPanel File Manager:**
1. Login to SiteGround cPanel
2. Go to **File Manager**
3. Navigate to `public_html/`
4. Create new folder: `install`
5. Upload all files from `siteground-installer/` to `/public_html/install/`

**Files to upload:**
- config.php
- index.php
- redirect.php
- .htaccess

**Via FTP (Alternative):**
```
Host: thecarbon6.agency
Port: 21
Path: /public_html/install/
```
Upload all 4 files.

---

### Step 2: Set Admin Password

Edit `config.php` line 8:
```php
define('ADMIN_PASSWORD', 'your-secure-password-here');
```

Change to a strong password (16+ characters).

---

### Step 3: Set Permissions

In cPanel File Manager, set these permissions:

**Folder:** `/public_html/install/` → **755**
**Files:** All PHP files → **644**
**Special:** `codes.db` (will be created) → **644**

---

### Step 4: Test It!

1. Visit: **thecarbon6.agency/install**
2. Enter your admin password
3. Click "Generate One-Time Code"
4. Get code like: `K7QW3M9P`
5. Full URL: `thecarbon6.agency/install/K7QW3M9P`

---

## ✅ DONE! Now Use It

### Generate Code:
1. Go to: **thecarbon6.agency/install**
2. Enter password
3. Choose expiry (1 hour - 30 days)
4. Click generate
5. Share the code URL

### User Experience:
1. Clicks: `thecarbon6.agency/install/K7QW3M9P`
2. **Redirects to installer** ✅
3. Code marked as used
4. Second person: **"Code Already Used"** ❌

---

## 📋 HOW IT WORKS

```
User visits: thecarbon6.agency/install/K7QW3M9P
    ↓
.htaccess rewrites to: redirect.php
    ↓
redirect.php checks SQLite database
    ↓
If valid & not used → Redirect to installer
If used → Error page
If expired → Error page
```

**Database:** SQLite (no MySQL setup needed!)
**Location:** `/public_html/install/codes.db` (auto-created)

---

## 🎨 FEATURES

✅ **One-Time Use** - Each code works exactly once
✅ **Auto-Expiry** - Set 1 hour to 30 days
✅ **Purpose Tracking** - Label codes for tracking
✅ **SQLite Database** - No MySQL configuration needed
✅ **Secure** - Password protected admin
✅ **Fast** - Runs on your existing hosting
✅ **Free** - No additional services required

---

## 🔒 SECURITY

### Database Protection:
- `.htaccess` blocks direct access to `codes.db`
- Config file not directly accessible
- Directory listing disabled

### Best Practices:
1. **Strong password** in config.php (16+ characters)
2. **HTTPS only** (SiteGround provides free SSL)
3. **Regular cleanup** (expired codes auto-delete)
4. **Monitor usage** (check database periodically)

---

## 🛠️ TROUBLESHOOTING

### "500 Internal Server Error"
**Cause:** .htaccess not working
**Fix:**
1. Check file permissions (644 for .htaccess)
2. Verify mod_rewrite is enabled (it is on SiteGround)
3. Check error logs in cPanel

### "Invalid Password"
**Cause:** Password mismatch
**Fix:**
1. Edit config.php
2. Change ADMIN_PASSWORD
3. Upload again

### "Database Error"
**Cause:** Permission issues
**Fix:**
1. Folder `/public_html/install/` needs write permission
2. Set folder to 755
3. Delete codes.db and reload page (will recreate)

### Codes Not Working
**Cause:** .htaccess rewrite not working
**Fix:**
1. Verify .htaccess uploaded correctly
2. Check file starts with "RewriteEngine On"
3. Contact SiteGround if mod_rewrite disabled

---

## 📊 CHECKING USAGE

### View Active Codes (Quick Method)

**Via phpMyAdmin:**
1. cPanel → phpMyAdmin
2. Select database
3. Import codes.db (or use SQLite Manager)

**Via SSH (Advanced):**
```bash
sqlite3 /home/username/public_html/install/codes.db
SELECT * FROM install_codes WHERE used = 0;
```

---

## 🔄 UPDATING

To update code length, expiry options, etc.:

**Edit config.php:**
```php
define('CODE_LENGTH', 8);  // Change to 6, 10, 12
define('DEFAULT_EXPIRY_HOURS', 24);
```

**Edit index.php:**
Find the `<select id="expiry">` section and add/remove options.

---

## 💾 BACKUP

**What to backup:**
- `/public_html/install/codes.db` (contains all codes)
- `/public_html/install/config.php` (your settings)

**Backup via cPanel:**
1. File Manager → Select `install` folder
2. Click "Compress"
3. Download ZIP file

---

## 🚫 UNINSTALL

To remove:
1. Delete `/public_html/install/` folder
2. Remove any backups

No database cleanup needed (uses SQLite file).

---

## 💰 COST

**FREE** - Uses your existing SiteGround hosting
- No additional services
- No monthly fees
- No usage limits

---

## 📞 SUPPORT

### SiteGround Support:
- If .htaccess issues: Contact SiteGround
- If PHP errors: Check PHP version (needs 7.4+)
- If SSL issues: Use SiteGround SSL setup

### Code Issues:
1. Check file permissions
2. Verify password in config.php
3. Check SiteGround error logs

---

## 🎯 COMPARISON

### Cloudflare Workers vs SiteGround PHP:

| Feature | Cloudflare | SiteGround |
|---------|-----------|------------|
| Setup Time | 10 min | 3 min ✅ |
| Complexity | Medium | Easy ✅ |
| New Account | Yes | No (existing) ✅ |
| Cost | Free tier | Included ✅ |
| Speed | Global CDN | Fast ✅ |
| Database | KV Store | SQLite ✅ |

**Verdict:** SiteGround is simpler since you already have it!

---

## 📁 FILE STRUCTURE

```
/public_html/install/
├── .htaccess          (URL rewriting)
├── config.php         (Settings)
├── index.php          (Admin interface)
├── redirect.php       (Code validator)
└── codes.db           (Auto-created database)
```

---

## ✅ FINAL CHECKLIST

- [ ] Files uploaded to `/public_html/install/`
- [ ] Admin password set in config.php
- [ ] File permissions correct (755 folder, 644 files)
- [ ] Test admin page: thecarbon6.agency/install
- [ ] Generate test code
- [ ] Test code redirect works
- [ ] Test code shows "used" on second try

---

**You're ready!** Generate codes at **thecarbon6.agency/install**

---

**Support Files:**
- config.php - Configuration
- index.php - Admin dashboard
- redirect.php - Code validator
- .htaccess - URL routing
- SETUP.md - This guide
