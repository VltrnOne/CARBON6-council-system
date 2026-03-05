#!/bin/bash
# CARBON[6] Auto-Deploy to SiteGround
# Automated deployment script

set -e

echo "════════════════════════════════════════════════════════════"
echo "  CARBON[6] - AUTOMATED SITEGROUND DEPLOYMENT"
echo "════════════════════════════════════════════════════════════"
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

# Configuration
SOURCE_DIR="/Users/Morpheous/sovereign-manifold-distribution/siteground-installer"
DOMAIN="thecarbon6.agency"

echo -e "${CYAN}Step 1: Collect SiteGround Credentials${NC}"
echo ""
read -p "SiteGround FTP Username: " FTP_USER
read -sp "SiteGround FTP Password: " FTP_PASS
echo ""
echo ""

read -p "Admin Password for Install System: " ADMIN_PASS
echo ""

echo -e "${CYAN}Step 2: Configure Files${NC}"
# Update admin password in config.php
sed -i.bak "s/your-secure-password-here/${ADMIN_PASS}/" "${SOURCE_DIR}/config.php"
echo -e "${GREEN}✓ Admin password configured${NC}"

echo ""
echo -e "${CYAN}Step 3: Upload via FTP${NC}"
echo "Connecting to ${DOMAIN}..."

# Create FTP script
cat > /tmp/carbon6-deploy.ftp << EOF
open ${DOMAIN}
user ${FTP_USER} ${FTP_PASS}
cd public_html
mkdir install
cd install
lcd ${SOURCE_DIR}
put config.php
put index.php
put redirect.php
put .htaccess
chmod 755 .
chmod 644 config.php
chmod 644 index.php
chmod 644 redirect.php
chmod 644 .htaccess
bye
EOF

# Execute FTP upload
ftp -n < /tmp/carbon6-deploy.ftp

echo -e "${GREEN}✓ Files uploaded${NC}"

# Cleanup
rm /tmp/carbon6-deploy.ftp
mv "${SOURCE_DIR}/config.php.bak" "${SOURCE_DIR}/config.php"

echo ""
echo "════════════════════════════════════════════════════════════"
echo -e "${GREEN}✅ DEPLOYMENT COMPLETE!${NC}"
echo "════════════════════════════════════════════════════════════"
echo ""
echo "Admin Panel: https://thecarbon6.agency/install"
echo "Password: ${ADMIN_PASS}"
echo ""
echo "Test it now:"
echo "1. Visit: https://thecarbon6.agency/install"
echo "2. Enter your admin password"
echo "3. Generate a test code"
echo ""
echo "Build. Own. Govern."
