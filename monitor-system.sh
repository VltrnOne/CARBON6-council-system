#!/bin/bash
# CARBON[6] Install Code System Monitor
# Real-time monitoring and alerts

set -e

DOMAIN="https://thecarbon6.agency/install"
CHECK_INTERVAL=300  # 5 minutes

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo "════════════════════════════════════════════════════════════"
echo "  CARBON[6] SYSTEM MONITOR"
echo "  Monitoring: ${DOMAIN}"
echo "  Interval: ${CHECK_INTERVAL}s (5 minutes)"
echo "════════════════════════════════════════════════════════════"
echo ""

check_system() {
    TIMESTAMP=$(date '+%Y-%m-%d %H:%M:%S')

    # Check admin page
    ADMIN_HTTP=$(curl -s -o /dev/null -w "%{http_code}" ${DOMAIN})

    # Check response time
    RESPONSE_TIME=$(curl -s -o /dev/null -w "%{time_total}" ${DOMAIN})

    echo -n "[${TIMESTAMP}] "

    if [ "$ADMIN_HTTP" = "200" ]; then
        echo -e "${GREEN}✅ System Online${NC} | Response: ${RESPONSE_TIME}s"
    else
        echo -e "${RED}❌ System Down (HTTP ${ADMIN_HTTP})${NC}"
        send_alert "System down: HTTP ${ADMIN_HTTP}"
    fi

    # Check SSL certificate
    SSL_DAYS=$(echo | openssl s_client -servername thecarbon6.agency -connect thecarbon6.agency:443 2>/dev/null | openssl x509 -noout -dates | grep notAfter | cut -d= -f2 | xargs -I {} date -j -f "%b %d %H:%M:%S %Y %Z" "{}" +%s)
    NOW=$(date +%s)
    DAYS_LEFT=$(( ($SSL_DAYS - $NOW) / 86400 ))

    if [ "$DAYS_LEFT" -lt 30 ]; then
        echo -e "  ${YELLOW}⚠️  SSL expires in ${DAYS_LEFT} days${NC}"
    fi
}

send_alert() {
    MESSAGE=$1
    echo ""
    echo "🚨 ALERT: ${MESSAGE}"
    echo ""

    # Could add email/Slack notifications here
    # curl -X POST "https://hooks.slack.com/..." -d "{\"text\":\"${MESSAGE}\"}"
}

# Main monitoring loop
while true; do
    check_system
    sleep ${CHECK_INTERVAL}
done
