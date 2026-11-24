#!/usr/bin/env bash
# ==============================================================================
# Script 03: Execute Ansible Playbook to Deploy Website
# Runs from Management Server against Target Web Server
# ==============================================================================
set -euo pipefail

cd /home/ubuntu/ansible

echo "============================================================"
echo ">>> Testing SSH Connectivity from Ansible Controller"
echo "============================================================"
ansible -i inventory.ini webservers -m ping

echo "============================================================"
echo ">>> Executing Ansible Deployment Playbook"
echo "============================================================"
ansible-playbook -i inventory.ini playbook.yml -v

echo "============================================================"
echo ">>> Verifying Website Deployment via Local Curl"
echo "============================================================"
curl -Is http://10.0.1.20 | head -n 5

echo ">>> Ansible Deployment Completed Successfully!"
