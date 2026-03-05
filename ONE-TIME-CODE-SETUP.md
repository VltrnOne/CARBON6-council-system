# CARBON[6] One-Time Install Code System

**Security Feature:** Generate unique, one-time use codes for controlled installer distribution.

---

## 🎯 How It Works

1. **You visit:** thecarbon6.agency/install
2. **Enter admin password** and click "Generate Code"
3. **Get unique code:** e.g., `K7QW3M9P`
4. **Share:** `thecarbon6.agency/install/K7QW3M9P`
5. **User clicks link** → redirects to installer (one time only)
6. **Second person tries same link** → "Code Already Used" error

---

## 🚀 Quick Setup (5 Minutes)

### Step 1: Install Cloudflare Wrangler

```bash
npm install -g wrangler
```

### Step 2: Login to Cloudflare

```bash
wrangler login
```

### Step 3: Create KV Namespace

```bash
wrangler kv:namespace create "INSTALL_CODES"
```

Copy the ID it gives you (looks like: `a1b2c3d4e5f6...`)

### Step 4: Update Configuration

Edit `wrangler.toml` and replace `YOUR_KV_NAMESPACE_ID` with the ID from Step 3.

### Step 5: Set Admin Password

Edit `cloudflare-worker.js` line 17:
```javascript
ADMIN_PASSWORD: 'YOUR_SECURE_PASSWORD_HERE', // ⚠️ Change this!
```

### Step 6: Deploy

```bash
wrangler deploy
```

### Step 7: Connect Your Domain

In Cloudflare Dashboard:
1. Go to **Workers & Pages** → Your worker
2. Click **Settings** → **Triggers** → **Add Custom Domain**
3. Add: `thecarbon6.agency/install*`

---

## ✅ Done! Now Test It

1. Visit: **thecarbon6.agency/install**
2. Enter your admin password
3. Click "Generate One-Time Code"
4. Share the code URL with someone
5. They click it → installer loads ✅
6. Try clicking again → "Code Already Used" ❌

---

## 📋 Features

✅ **One-Time Use** - Each code works exactly once
✅ **Expiration** - Set 1 hour to 30 days
✅ **Purpose Tracking** - Label codes (e.g., "Client Demo", "Partner Share")
✅ **Auto-Delete** - Expired codes automatically removed
✅ **Admin Dashboard** - Clean UI to generate codes
✅ **No Database Setup** - Uses Cloudflare KV (included free)

---

## 🎨 Customization

### Change Code Length
In `cloudflare-worker.js`:
```javascript
CODE_LENGTH: 8, // Change to 6, 10, 12, etc.
```

### Change Expiry Options
In the HTML section, modify the `<select>` options:
```html
<option value="1">1 hour</option>
<option value="24">24 hours</option>
<option value="168">7 days</option>
```

### Change Installer URL
In `cloudflare-worker.js`:
```javascript
INSTALLER_URL: 'https://vltrnone.github.io/CARBON6-council-system/',
```

---

## 🔒 Security

- Admin password required to generate codes
- Codes stored encrypted in Cloudflare KV
- Automatic expiration
- One-time use enforcement
- No public code listing

### Best Practices:
1. **Strong password** - Use 16+ characters
2. **Rotate password** - Change monthly
3. **Monitor usage** - Check which codes are used
4. **Short expiry** - Use 1-hour codes for demos

---

## 📊 Usage Examples

### Demo to Client
1. Generate code with 1-hour expiry
2. Purpose: "Acme Corp Demo"
3. Share: `thecarbon6.agency/install/A7X9K2MP`
4. Client installs within 1 hour
5. Code expires automatically

### Partner Distribution
1. Generate code with 7-day expiry
2. Purpose: "Partner Q1"
3. Share with partners
4. Track who used it

### Internal Team
1. Generate codes with 30-day expiry
2. Purpose: "Team Onboarding"
3. New team members get unique codes
4. Track installation compliance

---

## 🛠️ Troubleshooting

### "Invalid Password"
- Check you changed `ADMIN_PASSWORD` in worker code
- Redeploy: `wrangler deploy`

### "Invalid or Expired Code"
- Code may have expired
- Check expiry time when generated
- Generate a new code

### "Code Already Used"
- Code can only be used once
- Generate a new code for additional users

### Domain Not Working
- Verify custom domain in Cloudflare
- Check DNS is pointed to Cloudflare
- Wait 5 minutes for propagation

---

## 💰 Cost

**FREE for most usage:**
- Cloudflare Workers: 100,000 requests/day free
- KV Storage: 1GB free, 10M reads/day
- Custom domains: Included

**You'll likely never hit limits unless:**
- Generating 1000+ codes per day
- Or serving 100,000+ redirects per day

---

## 🔄 Alternative: Manual Code Management

If you don't want to set up Cloudflare, use this simpler approach:

### Create Short Links Manually:
1. Use **bit.ly** or **tinyurl.com**
2. Create custom short link
3. Delete link after one use
4. Repeat for each new user

**Pros:** No setup required
**Cons:** Manual process, not truly one-time use

---

## 📞 Support

If you need help setting this up:
1. Check you have a Cloudflare account (free)
2. Verify `thecarbon6.agency` is on Cloudflare DNS
3. Follow steps exactly in order
4. Contact Cloudflare support if domain issues

---

## 🎯 Summary

**Before:** Share long GitHub Pages URL publicly
**After:** Generate unique codes, share once, automatically expire

**Security Level:** 🔒🔒🔒🔒🔒

---

**Files:**
- `cloudflare-worker.js` - The worker code
- `wrangler.toml` - Configuration file
- `ONE-TIME-CODE-SETUP.md` - This guide

**Deploy Command:** `wrangler deploy`
**Admin URL:** thecarbon6.agency/install
**Code Format:** thecarbon6.agency/install/XXXXXXXX
