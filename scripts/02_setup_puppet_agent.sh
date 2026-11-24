#!/usr/bin/env bash
# ==============================================================================
# Script 02: Setup Puppet Agent on Target Web Server
# Connects to Puppet Master, submits CSR, signs CSR, and applies Apache manifest
# ==============================================================================
set -euo pipefail

echo "============================================================"
echo ">>> STEP 1: Installing Puppet Agent on Target Web Server"
echo "============================================================"
export DEBIAN_FRONTEND=noninteractive
sudo apt-get update -y
sudo apt-get install -y wget curl gnupg2

if ! command -v /opt/puppetlabs/bin/puppet &> /dev/null; then
    wget https://apt.puppet.com/puppet7-release-jammy.deb -O /tmp/puppet7-release.deb
    sudo dpkg -i /tmp/puppet7-release.deb
    sudo apt-get update -y
    sudo apt-get install -y puppet-agent
fi

# Configure Puppet Agent
sudo tee /etc/puppetlabs/puppet/puppet.conf > /dev/null << 'EOF'
[main]
certname = web-target.devops.lab
server = puppet-mgmt.devops.lab
environment = production
runinterval = 30m
EOF

# Ensure hosts mapping
grep -q "puppet-mgmt.devops.lab" /etc/hosts || echo "10.0.1.10 puppet-mgmt.devops.lab puppet" | sudo tee -a /etc/hosts
grep -q "web-target.devops.lab" /etc/hosts || echo "10.0.1.20 web-target.devops.lab web-target" | sudo tee -a /etc/hosts

echo "============================================================"
echo ">>> STEP 2: Submitting Certificate Signing Request (CSR)"
echo "============================================================"
sudo /opt/puppetlabs/bin/puppet ssl clean || true
# Initial agent run to register CSR with Master
sudo /opt/puppetlabs/bin/puppet agent -t || true

echo ">>> Puppet Agent configured on target server."
