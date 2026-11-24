# Complete Command Reference & Operations Manual

This document details all commands required to manage, execute, inspect, and verify the DevOps infrastructure.

---

## 1. Local Prerequisites & Shell Setup (Windows PowerShell)

```powershell
# Set environment PATH for Terraform and AWS CLI
$env:Path = "C:\Program Files\Amazon\AWSCLIV2;C:\Users\ASUS\AppData\Local\Microsoft\WinGet\Packages\Hashicorp.Terraform_Microsoft.Winget.Source_8wekyb3d8bbwe;$env:Path"

# Verify CLI tools
terraform -version
aws --version

# Set active AWS IAM profile
$env:AWS_PROFILE = "terraform-user"
aws sts get-caller-identity
```

---

## 2. Terraform (Infrastructure Provisioning)

```powershell
cd D:\DevOps-Project\terraform

# Initialize Terraform AWS provider
terraform init

# Validate configuration syntax
terraform validate

# Generate execution plan
terraform plan -out tfplan

# Apply infrastructure changes (creates VPC, SGs, EC2 instances)
terraform apply "tfplan"

# View outputs (IPs, URLs, SSH strings)
terraform output

# Destroy infrastructure when project evaluation is finished
terraform destroy
```

---

## 3. SSH Connection Commands

```powershell
# Connect to DevOps Management Server
ssh -i D:\DevOps-Project\keys\devops-key.pem ubuntu@<MGMT_SERVER_PUBLIC_IP>

# Connect to Target Web Server
ssh -i D:\DevOps-Project\keys\devops-key.pem ubuntu@<TARGET_SERVER_PUBLIC_IP>
```

---

## 4. Puppet Commands

### On Management Server (Puppet Server):
```bash
# Check Puppet Server service status
sudo systemctl status puppetserver

# List pending certificate requests from agents
sudo /opt/puppetlabs/bin/puppetserver ca list

# Sign certificate for Target Web Server
sudo /opt/puppetlabs/bin/puppetserver ca sign --certname web-target.devops.lab

# Check signed certificates
sudo /opt/puppetlabs/bin/puppetserver ca list --all
```

### On Target Web Server (Puppet Agent):
```bash
# Test connection and apply manifest from Puppet Server
sudo /opt/puppetlabs/bin/puppet agent -t

# Verify Apache service state enforced by Puppet
sudo systemctl status apache2
```

---

## 5. Ansible Commands (On Management Server)

```bash
cd /home/ubuntu/ansible

# Test SSH connection to inventory nodes
ansible -i inventory.ini webservers -m ping

# Run deployment playbook
ansible-playbook -i inventory.ini playbook.yml -v

# Check syntax of playbook
ansible-playbook -i inventory.ini playbook.yml --syntax-check
```

---

## 6. Nagios Core Commands (On Management Server)

```bash
# Verify Nagios configuration for syntax errors
sudo nagios4 -v /etc/nagios4/nagios.cfg

# Check Nagios daemon status
sudo systemctl status nagios4

# Restart Nagios to reload configuration changes
sudo systemctl restart nagios4

# Check Apache status (hosting Nagios web interface)
sudo systemctl status apache2
```

---

## 7. End-to-End Verification (From Windows Workstation)

```powershell
# Run the automated PowerShell verification suite
& D:\DevOps-Project\scripts\04_verify_all.ps1
```
