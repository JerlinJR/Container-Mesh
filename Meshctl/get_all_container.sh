#!/bin/bash

# Get all running container IDs
container_ids=$(docker ps -q)

# Iterate over each container ID
for container_id in $container_ids; do
    # Get the container name
    container_name=$(docker inspect --format '{{.Name}}' $container_id | cut -c 2-)

    # Get the WireGuard interface IP address if it exists
    wg_ip=$(docker exec $container_id ip -o -4 addr show wg0 2>/dev/null | awk '{print $4}' | cut -d'/' -f1)

    # Print container ID, container name, and WireGuard IP if available
    if [ -n "$wg_ip" ]; then
        echo "Container ID: $container_id, Container Name: $container_name, WireGuard IP: $wg_ip"
    else
        echo "Container ID: $container_id, Container Name: $container_name, WireGuard IP not found"
    fi
done
