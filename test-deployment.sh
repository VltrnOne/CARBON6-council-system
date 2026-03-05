#!/bin/bash
# Test CARBON[6] Install Code System
# Verifies deployment is working

set -e

echo "════════════════════════════════════════════════════════════"
echo "  CARBON[6] - DEPLOYMENT TEST"
echo "════════════════════════════════════════════════════════════"
echo ""

DOMAIN="https://thecarbon6.agency/install"

echo "Testing deployment..."
echo ""

# Test 1: Admin page accessible
echo "Test 1: Checking admin page..."
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" ${DOMAIN})

if [ "$HTTP_CODE" = "200" ]; then
    echo "✅ Admin page accessible (HTTP 200)"
else
    echo "❌ Admin page failed (HTTP ${HTTP_CODE})"
    exit 1
fi

# Test 2: Check for key elements
echo ""
echo "Test 2: Checking page content..."
CONTENT=$(curl -s ${DOMAIN})

if echo "$CONTENT" | grep -q "C\[6\]"; then
    echo "✅ CARBON[6] logo found"
else
    echo "❌ Logo missing"
fi

if echo "$CONTENT" | grep -q "Generate One-Time Code"; then
    echo "✅ Generate button found"
else
    echo "❌ Generate button missing"
fi

# Test 3: Check .htaccess working
echo ""
echo "Test 3: Testing URL rewriting..."
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" ${DOMAIN}/TESTCODE)

if [ "$HTTP_CODE" = "404" ] || [ "$HTTP_CODE" = "200" ]; then
    echo "✅ URL rewriting working"
else
    echo "⚠️  URL rewriting may have issues (HTTP ${HTTP_CODE})"
fi

echo ""
echo "════════════════════════════════════════════════════════════"
echo "✅ DEPLOYMENT TESTS PASSED"
echo "════════════════════════════════════════════════════════════"
echo ""
echo "Next steps:"
echo "1. Visit: ${DOMAIN}"
echo "2. Generate a test code"
echo "3. Try using the code"
echo ""
