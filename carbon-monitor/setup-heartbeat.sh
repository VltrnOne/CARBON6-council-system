#!/bin/bash
# CARBON[6] Heartbeat Setup Script
# Installs and configures the heartbeat service

set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo "════════════════════════════════════════════════════════════"
echo "  CARBON[6] Heartbeat Service Setup"
echo "════════════════════════════════════════════════════════════"
echo ""

# Install telemetry client script
echo "📦 Installing telemetry client..."
sudo cp telemetry-client.sh /usr/local/bin/carbon6-telemetry
sudo chmod +x /usr/local/bin/carbon6-telemetry
echo -e "${GREEN}✅ Telemetry client installed to /usr/local/bin/carbon6-telemetry${NC}"
echo ""

# Detect OS and install service
if [ "$(uname)" == "Darwin" ]; then
    echo "🍎 macOS detected - Installing LaunchAgent..."

    # Copy plist to LaunchAgents
    cp carbon6-heartbeat.plist ~/Library/LaunchAgents/

    # Load the service
    launchctl load ~/Library/LaunchAgents/carbon6-heartbeat.plist

    echo -e "${GREEN}✅ Heartbeat service installed and started${NC}"
    echo ""
    echo "Management commands:"
    echo "  Stop:   launchctl unload ~/Library/LaunchAgents/carbon6-heartbeat.plist"
    echo "  Start:  launchctl load ~/Library/LaunchAgents/carbon6-heartbeat.plist"
    echo "  Remove: launchctl unload ~/Library/LaunchAgents/carbon6-heartbeat.plist && rm ~/Library/LaunchAgents/carbon6-heartbeat.plist"

elif [ "$(expr substr $(uname -s) 1 5)" == "Linux" ]; then
    echo "🐧 Linux detected - Installing systemd service..."

    # Copy service file to systemd
    sudo cp carbon6-heartbeat.service /etc/systemd/system/

    # Reload systemd
    sudo systemctl daemon-reload

    # Enable and start service
    sudo systemctl enable carbon6-heartbeat.service
    sudo systemctl start carbon6-heartbeat.service

    echo -e "${GREEN}✅ Heartbeat service installed and started${NC}"
    echo ""
    echo "Management commands:"
    echo "  Status:  sudo systemctl status carbon6-heartbeat.service"
    echo "  Stop:    sudo systemctl stop carbon6-heartbeat.service"
    echo "  Start:   sudo systemctl start carbon6-heartbeat.service"
    echo "  Restart: sudo systemctl restart carbon6-heartbeat.service"
    echo "  Remove:  sudo systemctl stop carbon6-heartbeat.service && sudo systemctl disable carbon6-heartbeat.service"

else
    echo -e "${YELLOW}⚠️  Unsupported OS - Manual setup required${NC}"
    echo ""
    echo "To manually send heartbeats, run:"
    echo "  /usr/local/bin/carbon6-telemetry heartbeat"
    echo ""
    echo "You can set up a cron job to run every 5 minutes:"
    echo "  */5 * * * * /usr/local/bin/carbon6-telemetry heartbeat"
fi

echo ""
echo "════════════════════════════════════════════════════════════"
echo "  ✅ HEARTBEAT SERVICE READY"
echo "════════════════════════════════════════════════════════════"
echo ""
echo "The system will now send heartbeats every 5 minutes."
echo "You can test it manually:"
echo "  carbon6-telemetry heartbeat"
echo ""
