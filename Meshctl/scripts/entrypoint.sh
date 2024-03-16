#!/bin/bash

# Generate WireGuard key pair
cd /etc/wireguard
wg genkey | tee privatekey | wg pubkey > publickey

# Replace placeholders in the template with generated keys
sed -e "s|<PRIVATE_KEY>|$(cat privatekey)|" /etc/wireguard/wg0.conf.template > /etc/wireguard/wg0.conf


#Run the setupwg.sh to configure wireguard automatically
cd /bin
./setupwg.sh
cd /home
rm -rf ubuntu



# Start systemd
exec /lib/systemd/systemd
