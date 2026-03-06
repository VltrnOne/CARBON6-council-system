# CARBON[6] Real-Time Monitoring System

**Monitor all installations and active users in real-time**

---

## 🎯 What Is This?

Complete monitoring infrastructure to track:
- **Total Installations** - Every time someone installs CARBON[6]
- **Active Users** - Who's online in the last 24 hours
- **Live Systems** - Real-time active systems (last hour)
- **Platform Distribution** - macOS, Linux, Windows breakdown
- **Geographic Data** - Where installations are located

---

## 🏗️ System Components

### 1. Telemetry Collection (`telemetry.php`)
- REST API endpoint for data collection
- Three action types:
  - **install** - New installation reported
  - **heartbeat** - System sends "I'm alive" signal
  - **event** - Custom events (startup, shutdown, etc.)
- SQLite database storage (no MySQL needed)
- Automatic IP geolocation (ready for API integration)

### 2. Live Dashboard (`dashboard.php`)
- Password-protected admin interface
- Real-time statistics with auto-refresh
- Beautiful CARBON[6] branded UI
- Tables showing:
  - Total installations
  - Active users (last 24h)
  - Live systems (last hour)
  - Platform distribution
  - Recent installations
  - Active system details

### 3. Telemetry Client (`telemetry-client.sh`)
- Bash script for sending telemetry data
- Cross-platform (macOS, Linux, Windows/WSL)
- Unique installation ID generation
- OS detection and system information
- Silent operation (no user interruption)

### 4. Heartbeat Service
- Automated periodic reporting
- Runs every 5 minutes
- systemd service for Linux
- LaunchAgent for macOS
- Manual cron option for other systems

---

## 🚀 Deployment

### Upload to SiteGround

1. **Via FTP:**
   ```bash
   # Upload carbon-monitor directory to:
   /public_html/carbon-monitor/
   ```

2. **Set Permissions:**
   ```bash
   chmod 755 /public_html/carbon-monitor/*.php
   chmod 755 /public_html/carbon-monitor/*.sh
   chmod 666 /public_html/carbon-monitor/carbon_telemetry.db
   ```

3. **Configure Dashboard Password:**
   Edit `dashboard.php` line 8:
   ```php
   $DASHBOARD_PASSWORD = 'your-secure-password-here';
   ```

4. **Test Access:**
   - Dashboard: `https://thecarbon6.agency/carbon-monitor/dashboard.php`
   - API: `https://thecarbon6.agency/carbon-monitor/telemetry.php`

---

## 📊 How It Works

### Installation Flow

```
User runs installer
    ↓
Installer calls telemetry-client.sh
    ↓
telemetry-client.sh sends POST to telemetry.php
    ↓
telemetry.php stores data in SQLite
    ↓
Dashboard shows new installation
```

### Heartbeat Flow

```
Heartbeat service runs (every 5 min)
    ↓
carbon6-telemetry heartbeat
    ↓
POST to telemetry.php
    ↓
Dashboard shows system as "Active"
```

---

## 🔧 Usage

### Access Dashboard

1. Visit: `https://thecarbon6.agency/carbon-monitor/dashboard.php`
2. Enter admin password
3. View real-time statistics
4. Auto-refreshes every 30 seconds

### Manual Telemetry Commands

```bash
# Send installation report
carbon6-telemetry install K7QW3M9P

# Send heartbeat
carbon6-telemetry heartbeat

# Send custom event
carbon6-telemetry event "system_startup"
```

### Setup Heartbeat Service

```bash
cd carbon-monitor
chmod +x setup-heartbeat.sh
./setup-heartbeat.sh
```

This will:
- Install telemetry client to `/usr/local/bin/`
- Setup systemd service (Linux) or LaunchAgent (macOS)
- Start the service automatically
- Send heartbeats every 5 minutes

---

## 📡 API Endpoints

### POST /telemetry.php

**Install Action:**
```json
{
  "action": "install",
  "install_id": "abc123...",
  "install_code": "K7QW3M9P",
  "os": "macOS",
  "os_version": "14.2",
  "architecture": "arm64",
  "method": "installer"
}
```

**Heartbeat Action:**
```json
{
  "action": "heartbeat",
  "install_id": "abc123...",
  "status": "active",
  "uptime": 86400,
  "version": "1.0.0"
}
```

**Event Action:**
```json
{
  "action": "event",
  "install_id": "abc123...",
  "event_type": "startup",
  "event_data": {}
}
```

---

## 🗄️ Database Schema

### installations
```sql
CREATE TABLE installations (
    id INTEGER PRIMARY KEY,
    install_id TEXT UNIQUE NOT NULL,
    install_code TEXT,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    os TEXT,
    os_version TEXT,
    architecture TEXT,
    ip_address TEXT,
    country TEXT,
    region TEXT,
    city TEXT,
    user_agent TEXT,
    installation_method TEXT,
    system_info TEXT
);
```

### heartbeats
```sql
CREATE TABLE heartbeats (
    id INTEGER PRIMARY KEY,
    install_id TEXT NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    status TEXT DEFAULT 'active',
    uptime_seconds INTEGER,
    cpu_usage REAL,
    memory_usage REAL,
    active_agents INTEGER,
    system_version TEXT,
    FOREIGN KEY (install_id) REFERENCES installations(install_id)
);
```

### events
```sql
CREATE TABLE events (
    id INTEGER PRIMARY KEY,
    install_id TEXT NOT NULL,
    event_type TEXT NOT NULL,
    event_data TEXT,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (install_id) REFERENCES installations(install_id)
);
```

---

## 🔒 Security

### Dashboard Protection
- Password-protected admin interface
- Session-based authentication
- No public API access without auth

### Database Security
- SQLite file should be read/write by web server only
- Consider adding `.htaccess` to protect `*.db` files
- Regular backups recommended

### Privacy
- IP addresses collected for geolocation only
- No personally identifiable information stored
- Users can opt-out by not installing heartbeat service

---

## 📈 Metrics Explained

### Total Installations
- Count of all unique install_id entries
- Includes both active and inactive systems

### Active Now (24h)
- Systems that sent a heartbeat in last 24 hours
- Good indicator of daily active users

### Live Systems (1h)
- Systems that sent heartbeat in last hour
- Shows currently running systems

### Engagement Rate
- (Active 24h / Total Installations) * 100
- Measures system usage vs install base

---

## 🛠️ Troubleshooting

### Dashboard Shows 0 Installations
- Check that telemetry.php is accessible
- Verify database file permissions
- Test with: `curl -X POST https://thecarbon6.agency/carbon-monitor/telemetry.php`

### Heartbeat Not Working
- Verify service is running:
  - macOS: `launchctl list | grep carbon6`
  - Linux: `systemctl status carbon6-heartbeat`
- Check telemetry client is installed: `which carbon6-telemetry`
- Test manually: `carbon6-telemetry heartbeat`

### Permission Errors
- Ensure web server can write to database
- Check SQLite file permissions: `chmod 666 carbon_telemetry.db`
- Check directory permissions: `chmod 755 carbon-monitor/`

---

## 🎨 Customization

### Change Dashboard Password
Edit `dashboard.php` line 8:
```php
$DASHBOARD_PASSWORD = 'new-password';
```

### Adjust Heartbeat Frequency
**macOS (`carbon6-heartbeat.plist`):**
```xml
<key>StartInterval</key>
<integer>300</integer>  <!-- seconds -->
```

**Linux (`carbon6-heartbeat.service`):**
```ini
ExecStart=/bin/bash -c 'while true; do /usr/local/bin/carbon6-telemetry heartbeat; sleep 300; done'
#                                                                                    ^^^ seconds
```

### Add Geolocation API
Edit `telemetry.php` function `getIpInfo()`:
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

## 📦 File Structure

```
carbon-monitor/
├── telemetry.php              ← API endpoint
├── dashboard.php              ← Admin dashboard
├── telemetry-client.sh        ← Client script
├── setup-heartbeat.sh         ← Service installer
├── carbon6-heartbeat.service  ← Linux systemd
├── carbon6-heartbeat.plist    ← macOS LaunchAgent
├── carbon_telemetry.db        ← SQLite database (created automatically)
└── README.md                  ← This file
```

---

## 🎯 Next Steps

1. **Deploy to SiteGround**
   - Upload `carbon-monitor/` folder
   - Set dashboard password
   - Test access

2. **Update GitHub Repository**
   - Push `telemetry-client.sh` to GitHub
   - Installer will auto-download it

3. **Test End-to-End**
   - Run installer with code
   - Check dashboard for new installation
   - Setup heartbeat service
   - Verify active status appears

4. **Monitor and Iterate**
   - Watch installation patterns
   - Identify active vs inactive users
   - Improve based on usage data

---

**Build. Own. Govern.**

*Real-time visibility into your global distribution.*
