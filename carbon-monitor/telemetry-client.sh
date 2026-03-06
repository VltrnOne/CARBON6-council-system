#!/bin/bash
# CARBON[6] Telemetry Client
# Sends installation and heartbeat data to monitoring system

TELEMETRY_URL="https://thecarbon6.agency/carbon-monitor/telemetry.php"

# Generate unique install ID
generate_install_id() {
    if [ -f "$HOME/.carbon6_install_id" ]; then
        cat "$HOME/.carbon6_install_id"
    else
        # Generate new UUID-like ID
        install_id=$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 32 | head -n 1)
        echo "$install_id" > "$HOME/.carbon6_install_id"
        echo "$install_id"
    fi
}

# Detect OS information
detect_os() {
    if [ "$(uname)" == "Darwin" ]; then
        echo "macOS"
    elif [ "$(expr substr $(uname -s) 1 5)" == "Linux" ]; then
        if [ -f /etc/os-release ]; then
            . /etc/os-release
            echo "Linux-$ID"
        else
            echo "Linux"
        fi
    elif [ "$(expr substr $(uname -s) 1 10)" == "MINGW32_NT" ] || [ "$(expr substr $(uname -s) 1 10)" == "MINGW64_NT" ]; then
        echo "Windows"
    else
        echo "Unknown"
    fi
}

# Get OS version
get_os_version() {
    if [ "$(uname)" == "Darwin" ]; then
        sw_vers -productVersion
    elif [ "$(expr substr $(uname -s) 1 5)" == "Linux" ]; then
        if [ -f /etc/os-release ]; then
            . /etc/os-release
            echo "$VERSION_ID"
        fi
    fi
}

# Get architecture
get_architecture() {
    uname -m
}

# Send installation telemetry
send_install_telemetry() {
    local install_code="${1:-manual}"
    local install_id=$(generate_install_id)
    local os=$(detect_os)
    local os_version=$(get_os_version)
    local arch=$(get_architecture)

    # Build JSON payload
    local payload=$(cat <<EOF
{
  "action": "install",
  "install_id": "$install_id",
  "install_code": "$install_code",
  "os": "$os",
  "os_version": "$os_version",
  "architecture": "$arch",
  "method": "installer",
  "system_info": {
    "hostname": "$(hostname)",
    "user": "$(whoami)"
  }
}
EOF
)

    # Send to telemetry endpoint (silent)
    curl -s -X POST "$TELEMETRY_URL" \
        -H "Content-Type: application/json" \
        -d "$payload" > /dev/null 2>&1 || true

    echo "$install_id"
}

# Send heartbeat
send_heartbeat() {
    local install_id=$(generate_install_id)

    # Calculate uptime (seconds since last reboot)
    local uptime_seconds=0
    if [ "$(uname)" == "Darwin" ]; then
        uptime_seconds=$(sysctl -n kern.boottime | awk '{print $4}' | sed 's/,//')
        uptime_seconds=$(($(date +%s) - uptime_seconds))
    elif [ "$(expr substr $(uname -s) 1 5)" == "Linux" ]; then
        uptime_seconds=$(cat /proc/uptime | awk '{print int($1)}')
    fi

    # Build JSON payload
    local payload=$(cat <<EOF
{
  "action": "heartbeat",
  "install_id": "$install_id",
  "status": "active",
  "uptime": $uptime_seconds,
  "version": "1.0.0"
}
EOF
)

    # Send heartbeat (silent)
    curl -s -X POST "$TELEMETRY_URL" \
        -H "Content-Type: application/json" \
        -d "$payload" > /dev/null 2>&1 || true
}

# Send event
send_event() {
    local event_type="$1"
    local event_data="${2:-{}}"
    local install_id=$(generate_install_id)

    local payload=$(cat <<EOF
{
  "action": "event",
  "install_id": "$install_id",
  "event_type": "$event_type",
  "event_data": $event_data
}
EOF
)

    curl -s -X POST "$TELEMETRY_URL" \
        -H "Content-Type: application/json" \
        -d "$payload" > /dev/null 2>&1 || true
}

# Main function
case "${1:-help}" in
    install)
        send_install_telemetry "$2"
        ;;
    heartbeat)
        send_heartbeat
        ;;
    event)
        send_event "$2" "$3"
        ;;
    *)
        echo "Usage: $0 {install|heartbeat|event} [args]"
        echo ""
        echo "Examples:"
        echo "  $0 install K7QW3M9P       # Report installation with code"
        echo "  $0 heartbeat              # Send heartbeat"
        echo "  $0 event 'startup'        # Send event"
        exit 1
        ;;
esac
