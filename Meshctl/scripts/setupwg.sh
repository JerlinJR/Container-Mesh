#!/bin/bash

API_URL="http://94.237.79.8/api/wg/add_peer"
PUBLIC_KEY_FILE="/etc/wireguard/publickey"
PRIVATE_KEY_FILE="/etc/wireguard/privatekey"
WG_CONF_FILE="/etc/wireguard/wg0.conf"
EMAIL="testeee@gmail.com"

# Read the public key from the file and URL encode it
PUBLIC_KEY=$(cat "$PUBLIC_KEY_FILE" | tr -d '\n' | tr -d ' ' | jq -s -R -r @uri)

PRIVATE_KEY=$(cat "$PRIVATE_KEY_FILE")

response=$(curl -s -X POST -d "public_key=${PUBLIC_KEY}" -d "email=${EMAIL}" "${API_URL}")

http_status=$(echo "$response" | jq -r '.result')  # Extract the result field

echo "Response: $response"

# Function to check if an IP address is within a specific CIDR range
ip_in_cidr() {
    local ip=$1
    local cidr=$2

    IFS='/' read -r -a cidr_parts <<< "$cidr"
    local range_ip=${cidr_parts[0]}
    local range_mask=${cidr_parts[1]}

    IFS='.' read -r -a ip_parts <<< "$ip"
    IFS='.' read -r -a range_ip_parts <<< "$range_ip"

    for ((i = 0; i < range_mask / 8; i++)); do
        if [ "${ip_parts[i]}" != "${range_ip_parts[i]}" ]; then
            return 1  # Not in range
        fi
    done

    local remaining_bits=$((range_mask % 8))
    local mask=$((255 << (8 - remaining_bits)))

    if [ $((ip_parts[i] & mask)) != $((range_ip_parts[i] & mask)) ]; then
        return 1  # Not in range
    fi

    return 0  # In range
}

if [ -n "$http_status" ] && ip_in_cidr "$http_status" "172.20.0.0/16"; then
    echo "HTTPSTATUS: $http_status"
    # Extract the IP address and private key from the response
    IP_ADDRESS=$http_status  # The value is directly assigned
    
    # Replace <INTERNAL_IP> and <PRIVATE_KEY> in the WG_CONF_FILE
    sed -i "s/<INTERNAL_IP>/$IP_ADDRESS/" "$WG_CONF_FILE"
    sed -i "s/<PRIVATE_KEY>/$PRIVATE_KEY/" "$WG_CONF_FILE"

    echo "Configuration updated successfully. New IP address: $IP_ADDRESS"

    # Bring up the WireGuard interface
    wg-quick up wg0
else
    echo "Error: Unable to extract a valid IP address from the response or IP address is not within the specified CIDR range."
    echo "Response: $response"
fi
