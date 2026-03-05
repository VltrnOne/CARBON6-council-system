#!/bin/bash
# CARBON[6] Install System - Master CLI
# Unified interface for all operations

set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

show_banner() {
    clear
    echo ""
    echo -e "${BLUE}╔════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${BLUE}║${NC}                                                            ${BLUE}║${NC}"
    echo -e "${BLUE}║${NC}           ${CYAN}CARBON[6] INSTALL SYSTEM - CLI${NC}              ${BLUE}║${NC}"
    echo -e "${BLUE}║${NC}                                                            ${BLUE}║${NC}"
    echo -e "${BLUE}║${NC}        ${GREEN}One-Time Code Generation & Management${NC}          ${BLUE}║${NC}"
    echo -e "${BLUE}║${NC}                                                            ${BLUE}║${NC}"
    echo -e "${BLUE}╚════════════════════════════════════════════════════════════╝${NC}"
    echo ""
}

show_menu() {
    echo ""
    echo -e "${CYAN}═══ DEPLOYMENT ═══${NC}"
    echo "  1. Deploy to SiteGround"
    echo "  2. Test Deployment"
    echo "  3. Update Files"
    echo ""
    echo -e "${CYAN}═══ CODE MANAGEMENT ═══${NC}"
    echo "  4. Generate New Code"
    echo "  5. Check Code Status"
    echo "  6. View Analytics"
    echo ""
    echo -e "${CYAN}═══ MONITORING ═══${NC}"
    echo "  7. System Status"
    echo "  8. Start Monitor (background)"
    echo "  9. View Logs"
    echo ""
    echo -e "${CYAN}═══ UTILITIES ═══${NC}"
    echo "  10. Backup Database"
    echo "  11. Documentation"
    echo "  12. Exit"
    echo ""
    read -p "Select option: " choice

    case $choice in
        1) deploy ;;
        2) test_deploy ;;
        3) update ;;
        4) generate ;;
        5) check ;;
        6) analytics ;;
        7) status ;;
        8) start_monitor ;;
        9) view_logs ;;
        10) backup ;;
        11) docs ;;
        12) exit 0 ;;
        *) echo -e "${RED}Invalid option${NC}"; sleep 1; show_banner; show_menu ;;
    esac
}

deploy() {
    echo ""
    echo -e "${CYAN}Starting deployment...${NC}"
    ./deploy-to-siteground.sh
    show_banner
    show_menu
}

test_deploy() {
    echo ""
    echo -e "${CYAN}Testing deployment...${NC}"
    ./test-deployment.sh
    read -p "Press Enter to continue..."
    show_banner
    show_menu
}

update() {
    echo ""
    echo -e "${CYAN}Updating files...${NC}"
    echo "Running deployment in update mode..."
    ./deploy-to-siteground.sh
    show_banner
    show_menu
}

generate() {
    ./manage-codes.sh
    show_banner
    show_menu
}

check() {
    ./manage-codes.sh
    show_banner
    show_menu
}

analytics() {
    echo ""
    echo -e "${CYAN}System Analytics${NC}"
    echo ""

    DOMAIN="https://thecarbon6.agency/install"

    # Check status
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" ${DOMAIN})
    RESPONSE_TIME=$(curl -s -o /dev/null -w "%{time_total}" ${DOMAIN})

    echo "System Health:"
    if [ "$HTTP_CODE" = "200" ]; then
        echo -e "  Status: ${GREEN}✅ Online${NC}"
    else
        echo -e "  Status: ${RED}❌ Offline (HTTP ${HTTP_CODE})${NC}"
    fi

    echo -e "  Response Time: ${RESPONSE_TIME}s"
    echo -e "  URL: ${DOMAIN}"

    echo ""
    read -p "Press Enter to continue..."
    show_banner
    show_menu
}

status() {
    analytics
}

start_monitor() {
    echo ""
    echo -e "${CYAN}Starting system monitor...${NC}"
    echo "Monitor will run in background and check every 5 minutes."
    echo ""

    nohup ./monitor-system.sh > monitor.log 2>&1 &
    MONITOR_PID=$!

    echo -e "${GREEN}✅ Monitor started (PID: ${MONITOR_PID})${NC}"
    echo "View logs: tail -f monitor.log"
    echo "Stop: kill ${MONITOR_PID}"
    echo ""

    read -p "Press Enter to continue..."
    show_banner
    show_menu
}

view_logs() {
    echo ""
    echo -e "${CYAN}Viewing logs...${NC}"
    echo ""

    if [ -f "monitor.log" ]; then
        tail -n 50 monitor.log
    else
        echo "No logs found. Start monitor first."
    fi

    echo ""
    read -p "Press Enter to continue..."
    show_banner
    show_menu
}

backup() {
    echo ""
    echo -e "${CYAN}Backup System${NC}"
    echo ""
    echo "To backup the database:"
    echo "1. Login to SiteGround cPanel"
    echo "2. Go to File Manager"
    echo "3. Navigate to: /public_html/install/"
    echo "4. Download: codes.db"
    echo ""
    echo "Or via FTP:"
    echo "  Download: /public_html/install/codes.db"
    echo ""

    read -p "Press Enter to continue..."
    show_banner
    show_menu
}

docs() {
    echo ""
    echo -e "${CYAN}Documentation${NC}"
    echo ""
    echo "Available guides:"
    echo "  • siteground-installer/README.md - Quick start"
    echo "  • siteground-installer/SETUP.md - Complete setup"
    echo ""
    echo "Online:"
    echo "  • Admin: https://thecarbon6.agency/install"
    echo "  • GitHub: https://github.com/VltrnOne/CARBON6-council-system"
    echo ""

    read -p "Press Enter to continue..."
    show_banner
    show_menu
}

# Start
show_banner
show_menu
