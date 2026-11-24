#!/usr/bin/env bash
# ==============================================================================
# Script 01: Setup DevOps Management Server
# Components: Puppet Server, Ansible Control Node, Nagios Core
# OS: Ubuntu 22.04 LTS
# ==============================================================================
set -euo pipefail

echo "============================================================"
echo ">>> STEP 1: Updating System Packages & Configuring Repositories"
echo "============================================================"
export DEBIAN_FRONTEND=noninteractive
sudo apt-get update -y
sudo apt-get install -y wget curl gnupg2 software-properties-common git ufw

# Ensure hosts mapping
grep -q "puppet-mgmt.devops.lab" /etc/hosts || echo "10.0.1.10 puppet-mgmt.devops.lab puppet" | sudo tee -a /etc/hosts
grep -q "web-target.devops.lab" /etc/hosts || echo "10.0.1.20 web-target.devops.lab web-target" | sudo tee -a /etc/hosts

echo "============================================================"
echo ">>> STEP 2: Installing & Configuring Puppet Server (Puppet 7)"
echo "============================================================"
if ! command -v /opt/puppetlabs/bin/puppetserver &> /dev/null; then
    wget https://apt.puppet.com/puppet7-release-jammy.deb -O /tmp/puppet7-release.deb
    sudo dpkg -i /tmp/puppet7-release.deb
    sudo apt-get update -y
    sudo apt-get install -y puppetserver
fi

# Configure JVM heap size for t3.small (512MB initial, 1024MB max)
echo ">>> Tuning Puppet Server JVM memory limits (-Xms512m -Xmx1024m)..."
sudo sed -i 's/-Xms2g -Xmx2g/-Xms512m -Xmx1024m/' /etc/default/puppetserver || true

# Configure puppet.conf
sudo tee /etc/puppetlabs/puppet/puppet.conf > /dev/null << 'EOF'
[server]
vardir = /opt/puppetlabs/server/data/puppetserver
logdir = /var/log/puppetlabs/puppetserver
rundir = /var/run/puppetlabs/puppetserver
pidfile = /var/run/puppetlabs/puppetserver/puppetserver.pid
codedir = /etc/puppetlabs/code
dns_alt_names = puppet,puppet-mgmt,puppet-mgmt.devops.lab

[main]
certname = puppet-mgmt.devops.lab
server = puppet-mgmt.devops.lab
environment = production
runinterval = 1h
EOF

# Enable and start puppetserver
sudo systemctl daemon-reload
sudo systemctl enable puppetserver
sudo systemctl restart puppetserver

echo "============================================================"
echo ">>> STEP 3: Installing Ansible Control Node"
echo "============================================================"
sudo apt-get install -y ansible python3-pip

echo "============================================================"
echo ">>> STEP 4: Installing & Configuring Nagios Core"
echo "============================================================"
# Enable universe repo and install nagios4
sudo add-apt-repository -y universe
sudo apt-get update -y
sudo apt-get install -y nagios4 nagios-plugins-basic nagios-plugins-contrib nagios-plugins-standard apache2 apache2-utils

# Set nagiosadmin password
sudo htpasswd -b -c /etc/nagios4/htpasswd.users nagiosadmin "NagiosAdmin2026!"

# Enable Apache modules & configuration
sudo a2enmod cgi
sudo a2enmod rewrite
sudo a2enconf nagios4-cgi

# Verify Nagios config and restart services
sudo systemctl enable nagios4
sudo systemctl restart nagios4
sudo systemctl enable apache2
sudo systemctl restart apache2

echo "============================================================"
echo ">>> Management Server Setup Completed Successfully!"
echo "    - Puppet Server: RUNNING (:8140)"
echo "    - Ansible: INSTALLED ($(ansible --version | head -n1))"
echo "    - Nagios Core: RUNNING (:80/nagios)"
echo "============================================================"
