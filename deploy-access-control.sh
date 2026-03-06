#!/bin/bash
# Deploy Access Control System to SiteGround

echo "════════════════════════════════════════════════════════════"
echo "  CARBON[6] Access Control System Deployment"
echo "════════════════════════════════════════════════════════════"
echo ""
echo "📦 This will update:"
echo "  ✓ dashboard.php - Added 'Manage Access' button"
echo "  ✓ manage-access.php - NEW: User access management interface"
echo "  ✓ access-control.php - NEW: API for suspend/revoke operations"
echo "  ✓ telemetry.php - Updated with access control checks"
echo ""

# Copy updated files to Desktop for easy upload
echo "📋 Copying files to Desktop..."
mkdir -p ~/Desktop/carbon6-access-control-update

cp carbon-monitor/dashboard.php ~/Desktop/carbon6-access-control-update/
cp carbon-monitor/manage-access.php ~/Desktop/carbon6-access-control-update/
cp carbon-monitor/access-control.php ~/Desktop/carbon6-access-control-update/
cp carbon-monitor/telemetry.php ~/Desktop/carbon6-access-control-update/

echo ""
echo "✅ Files ready at: ~/Desktop/carbon6-access-control-update/"
echo ""
echo "════════════════════════════════════════════════════════════"
echo "  📤 UPLOAD INSTRUCTIONS"
echo "════════════════════════════════════════════════════════════"
echo ""
echo "1. Go to SiteGround File Manager"
echo "2. Navigate to: public_html/carbon-monitor/"
echo "3. Upload these 4 files (replace existing):"
echo "   - dashboard.php"
echo "   - manage-access.php (NEW)"
echo "   - access-control.php (NEW)"
echo "   - telemetry.php"
echo ""
echo "4. After upload, visit:"
echo "   https://thecarbon6.agency/carbon-monitor/dashboard.php"
echo ""
echo "5. Click '🔐 Manage Access' button to access user management"
echo ""
echo "════════════════════════════════════════════════════════════"
echo "  🎯 NEW FEATURES"
echo "════════════════════════════════════════════════════════════"
echo ""
echo "✓ Suspend installations temporarily"
echo "✓ Reactivate suspended installations"
echo "✓ Permanently revoke access with reason tracking"
echo "✓ Filter installations by status"
echo "✓ View suspension/revocation history"
echo "✓ Automatic telemetry blocking for suspended/revoked users"
echo ""
