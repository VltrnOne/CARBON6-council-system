# CARBON[6] Monitoring - Quick Start Guide

**Get real-time visibility in 5 minutes**

---

## ⚡ 5-Minute Setup

### Step 1: Deploy to SiteGround (2 minutes)

```bash
cd /Users/Morpheous/sovereign-manifold-distribution
./deploy-monitoring.sh
```

**Provide:**
- FTP username
- FTP password
- Dashboard admin password (you choose)

**Result:** Monitoring system live at `thecarbon6.agency/carbon-monitor/`

---

### Step 2: Push to GitHub (1 minute)

```bash
cd /Users/Morpheous/sovereign-manifold-distribution

# Add monitoring files
git add carbon-monitor/telemetry-client.sh
git add carbon-monitor/setup-heartbeat.sh
git add carbon-monitor/carbon6-heartbeat.service
git add carbon-monitor/carbon6-heartbeat.plist

# Commit
git commit -m "Add CARBON[6] real-time monitoring system"

# Push
git push origin main
```

**Result:** Installers can now auto-download telemetry client

---

### Step 3: Test Dashboard (1 minute)

1. Visit: `https://thecarbon6.agency/carbon-monitor/dashboard.php`
2. Enter your admin password
3. Should see empty dashboard (no installations yet)

---

### Step 4: Test Installation (1 minute)

**Generate test code:**
1. Go to: `https://thecarbon6.agency/install`
2. Generate a code (e.g., `TEST1234`)

**Run installer:**
```bash
curl -sSL https://vltrnone.github.io/CARBON6-council-system/install.sh | INSTALL_CODE=TEST1234 bash
```

**Check dashboard:**
- Refresh dashboard
- Should see 1 installation
- Platform: macOS (or your OS)
- Status: Just installed

---

### Step 5: Enable Heartbeat (optional)

**On installed system:**
```bash
cd ~/sovereign-manifold/council  # or wherever installed
curl -sSL https://raw.githubusercontent.com/VltrnOne/CARBON6-council-system/main/carbon-monitor/setup-heartbeat.sh | bash
```

**Result:** System sends heartbeats every 5 minutes

---

## 📊 What You Get

### Dashboard Shows:

**Statistics:**
- Total installations
- Active users (last 24h)
- Live systems (last hour)
- Engagement rate

**Tables:**
- Platform distribution (macOS, Linux, Windows)
- Live systems with status
- Recent installations
- Geographic data

**Features:**
- Auto-refresh every 30 seconds
- Beautiful CARBON[6] branded UI
- Real-time updates
- Installation tracking

---

## 🎯 Common Tasks

### View Active Users
```
Dashboard → "Active Now" card
Shows systems active in last 24 hours
```

### Check Live Systems
```
Dashboard → "Live Systems (Last Hour)" table
Shows currently running systems with timestamps
```

### Track Installation Growth
```
Dashboard → "Recent Installations" table
See who installed when and from where
```

### Manual Heartbeat Test
```bash
# On installed system
carbon6-telemetry heartbeat
```

---

## 🔧 Troubleshooting

### "Page Not Found" Error
- Check FTP upload completed successfully
- Verify URL: `thecarbon6.agency/carbon-monitor/dashboard.php`
- Check file permissions on server

### "0 Installations" Showing
- Ensure installers have been updated with telemetry code
- Check GitHub files were pushed
- Test telemetry API manually:
  ```bash
  curl -X POST https://thecarbon6.agency/carbon-monitor/telemetry.php \
    -H "Content-Type: application/json" \
    -d '{"action":"heartbeat","install_id":"test"}'
  ```

### Heartbeat Not Working
- Verify service is installed: `launchctl list | grep carbon6` (macOS)
- Test manually: `carbon6-telemetry heartbeat`
- Check if telemetry client is in PATH: `which carbon6-telemetry`

---

## 🚀 Production Checklist

- [ ] Monitoring system deployed to SiteGround
- [ ] Dashboard password set and secure
- [ ] Telemetry client pushed to GitHub
- [ ] Installation scripts updated with telemetry
- [ ] Dashboard accessible and shows data
- [ ] Test installation completed successfully
- [ ] Heartbeat service tested and working
- [ ] Documentation reviewed

---

## 📈 Monitoring Best Practices

### Daily
- Check dashboard for new installations
- Review active user count
- Monitor engagement rate

### Weekly
- Analyze platform distribution trends
- Check for inactive installations
- Review geographic spread

### Monthly
- Calculate growth metrics
- Compare active vs total installations
- Plan improvements based on data

---

## 🎨 Customization

### Change Refresh Rate
Edit `dashboard.php` line 302:
```javascript
setTimeout(() => location.reload(), 30000); // 30 seconds
```

### Add Custom Metrics
Add to dashboard SQL queries for:
- Installation method breakdown
- Version distribution
- Peak usage times
- Custom events

### Enhance Geolocation
Integrate IP geolocation API in `telemetry.php`:
```php
function getIpInfo($ip) {
    $response = file_get_contents("https://ipapi.co/$ip/json/");
    $data = json_decode($response, true);
    return [
        'country' => $data['country_name'] ?? null,
        'region' => $data['region'] ?? null,
        'city' => $data['city'] ?? null
    ];
}
```

---

## 📞 Support

**Full Documentation:** `carbon-monitor/README.md`

**Common Issues:** Check troubleshooting section above

**Questions:** Review main documentation for detailed setup

---

**Build. Own. Govern.**

*Know your users. Optimize your distribution. Scale with confidence.*

---

**Status:** ✅ PRODUCTION READY
**Deploy Time:** 5 minutes
**Maintenance:** Minimal (SQLite auto-manages)
**Cost:** $0 (uses existing SiteGround hosting)
