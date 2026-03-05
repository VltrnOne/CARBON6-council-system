#!/bin/bash
# CARBON[6] Install Code Manager CLI
# Manage codes from command line

set -e

DOMAIN="https://thecarbon6.agency/install"

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
RED='\033[0;31m'
NC='\033[0m'

show_menu() {
    echo ""
    echo "════════════════════════════════════════════════════════════"
    echo -e "  ${CYAN}CARBON[6] INSTALL CODE MANAGER${NC}"
    echo "════════════════════════════════════════════════════════════"
    echo ""
    echo "1. Generate New Code"
    echo "2. Check Code Status"
    echo "3. List All Codes"
    echo "4. View Code Analytics"
    echo "5. Test System"
    echo "6. Exit"
    echo ""
    read -p "Select option: " choice

    case $choice in
        1) generate_code ;;
        2) check_code ;;
        3) list_codes ;;
        4) analytics ;;
        5) test_system ;;
        6) exit 0 ;;
        *) echo "Invalid option"; show_menu ;;
    esac
}

generate_code() {
    echo ""
    echo -e "${CYAN}Generate New Code${NC}"
    echo ""

    read -p "Admin Password: " ADMIN_PASS
    read -p "Purpose (e.g., Client Demo): " PURPOSE
    read -p "Expiry (1=1hr, 24=1day, 168=7days): " EXPIRY

    echo ""
    echo "Generating code..."

    RESPONSE=$(curl -s -X POST ${DOMAIN} \
        -H "Content-Type: application/json" \
        -d "{\"password\":\"${ADMIN_PASS}\",\"purpose\":\"${PURPOSE}\",\"expiry\":${EXPIRY}}")

    if echo "$RESPONSE" | grep -q "error"; then
        echo -e "${RED}❌ Error: $(echo $RESPONSE | grep -o '"error":"[^"]*"' | cut -d'"' -f4)${NC}"
    else
        CODE=$(echo $RESPONSE | grep -o '"code":"[^"]*"' | cut -d'"' -f4)
        URL=$(echo $RESPONSE | grep -o '"url":"[^"]*"' | cut -d'"' -f4)

        echo ""
        echo -e "${GREEN}✅ Code Generated!${NC}"
        echo ""
        echo -e "Code: ${CYAN}${CODE}${NC}"
        echo -e "URL:  ${CYAN}${URL}${NC}"
        echo ""
        echo "Copied to clipboard:"
        echo "${URL}" | pbcopy
        echo -e "${GREEN}✓ Full URL copied${NC}"
    fi

    show_menu
}

check_code() {
    echo ""
    echo -e "${CYAN}Check Code Status${NC}"
    echo ""

    read -p "Enter code to check: " CODE

    echo ""
    echo "Checking code: ${CODE}..."

    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" ${DOMAIN}/${CODE})

    if [ "$HTTP_CODE" = "302" ]; then
        echo -e "${GREEN}✅ Code is valid and unused${NC}"
    elif [ "$HTTP_CODE" = "403" ]; then
        echo -e "${YELLOW}⚠️  Code already used${NC}"
    elif [ "$HTTP_CODE" = "404" ]; then
        echo -e "${RED}❌ Code invalid or expired${NC}"
    else
        echo -e "${YELLOW}? Unknown status (HTTP ${HTTP_CODE})${NC}"
    fi

    show_menu
}

list_codes() {
    echo ""
    echo -e "${CYAN}List All Codes${NC}"
    echo ""
    echo "This requires direct database access."
    echo "Use SiteGround phpMyAdmin or SSH to view codes.db"
    echo ""

    show_menu
}

analytics() {
    echo ""
    echo -e "${CYAN}Code Analytics${NC}"
    echo ""

    echo "Checking system health..."

    # Test admin page
    ADMIN_STATUS=$(curl -s -o /dev/null -w "%{http_code}" ${DOMAIN})

    echo ""
    echo "System Status:"
    if [ "$ADMIN_STATUS" = "200" ]; then
        echo -e "  Admin Page: ${GREEN}✅ Online${NC}"
    else
        echo -e "  Admin Page: ${RED}❌ Offline (HTTP ${ADMIN_STATUS})${NC}"
    fi

    echo ""
    echo "For detailed analytics:"
    echo "  • Login to SiteGround cPanel"
    echo "  • Open phpMyAdmin"
    echo "  • View install_codes table"

    show_menu
}

test_system() {
    echo ""
    echo -e "${CYAN}Testing System${NC}"
    echo ""

    ./test-deployment.sh

    show_menu
}

# Start
clear
show_menu
