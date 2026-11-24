# College Practical Project Screenshot Capture Guide

**Application:** CampusVoice — Student Complaint & Grievance Redressal Management System  
**Architecture:** Terraform • Puppet • Ansible • Nagios Core on AWS (`ap-south-1`)

Capture the following 18 screenshots in order for your practical report submission and lab viva:

---

## 📸 Practical Examination Screenshot Checklist

### 1. Terraform Phase (Infrastructure as Code)
- [ ] **Screenshot 1: Terraform Init & Validate**
  - Command: `terraform init` and `terraform validate` in `D:\DevOps-Project\terraform`
  - Output: `Terraform has been successfully initialized!` and `Success! The configuration is valid.`
- [ ] **Screenshot 2: Terraform Plan Output**
  - Command: `terraform plan`
  - Output: `Plan: 10 to add, 0 to change, 0 to destroy.` showing VPC, Subnet, SGs, Key Pair, and 2 EC2 instances.
- [ ] **Screenshot 3: Terraform Apply Complete & Outputs**
  - Command: `terraform apply`
  - Output: `Apply complete! Resources: 10 added, 0 changed, 0 destroyed.` with output IPs (`43.205.213.123` and `13.207.51.84`).
- [ ] **Screenshot 4: AWS EC2 Management Console**
  - Location: AWS Console > EC2 > Instances (Region: Mumbai `ap-south-1`)
  - Content: Both instances (`devops-college-project-mgmt-server` and `devops-college-project-target-server`) in `Running` state with `2/2 checks passed`.

---

### 2. Puppet Phase (Configuration Management)
- [ ] **Screenshot 5: Puppet Server Active Status**
  - Node: Management Server (`43.205.213.123`)
  - Command: `sudo systemctl status puppetserver`
  - Output: Active: `active (running)` with JVM memory tuned to 1GB max heap.
- [ ] **Screenshot 6: Puppet Certificate Signing**
  - Node: Management Server (`43.205.213.123`)
  - Command: `sudo /opt/puppetlabs/bin/puppetserver ca list --all`
  - Output: `web-target.devops.lab` certificate signed by Puppet CA.
- [ ] **Screenshot 7: Puppet Agent Catalog Run (Apache & PHP Runtime Enforcement)**
  - Node: Target Web Server (`13.207.51.84`)
  - Command: `sudo /opt/puppetlabs/bin/puppet agent -t`
  - Output: `Notice: /Stage[main]/Apache_web/Package[php]/ensure: created`, `Package[libapache2-mod-php]`, `Package[php-sqlite3]`, and `File[/var/www/html/data]`.
- [ ] **Screenshot 8: Apache2 and PHP Verification on Target Server**
  - Node: Target Web Server (`13.207.51.84`)
  - Command: `sudo systemctl status apache2 && php -v`
  - Output: `active (running)` and `PHP 8.1.x` installed.

---

### 3. Ansible Phase (Application Orchestration)
- [ ] **Screenshot 9: Ansible Ad-Hoc Ping Test**
  - Node: Management Server (`43.205.213.123`)
  - Command: `cd /home/ubuntu/ansible && ansible -i inventory.ini webservers -m ping`
  - Output: `web-target.devops.lab | SUCCESS => { "ping": "pong" }` in green.
- [ ] **Screenshot 10: Ansible Playbook Execution (CampusVoice Deployment)**
  - Node: Management Server (`43.205.213.123`)
  - Command: `ansible-playbook -i inventory.ini playbook.yml`
  - Output: Successful tasks, `TASK [Assert application and database health]`, and `PLAY RECAP: ok=12 changed=4 failed=0`.

---

### 4. Real-World Application Phase (Browser Verification)
- [ ] **Screenshot 11: CampusVoice Student Grievance Submission Portal**
  - URL: `http://13.207.51.84`
  - Content: Browser showing the Student Complaint form, metrics cards (Total, Pending, In Progress, Resolved), and recent grievances feed.
- [ ] **Screenshot 12: Successful Grievance Submission with Instant Ticket ID**
  - Action: Fill and submit a grievance on `http://13.207.51.84`
  - Content: Modal showing `Grievance Lodged Successfully!` with generated reference ticket ID (e.g. `GRV-2026-3399`).
- [ ] **Screenshot 13: Grievance Resolution Timeline Tracking**
  - URL: `http://13.207.51.84/track.php?query=GRV-2026-1001`
  - Content: Browser showing the 4-stage stepper timeline (`Pending` -> `Under Review` -> `In Progress` -> `Resolved`), student details, grievance description, and official administration response remarks.
- [ ] **Screenshot 14: CampusVoice Admin Coordinator Desk**
  - URL: `http://13.207.51.84/admin.php`
  - Content: Administrative table showing all student grievances, filters by category/department, and the status update action button.
- [ ] **Screenshot 15: Application & Database Health Check API**
  - URL: `http://13.207.51.84/health.php`
  - Content: Browser showing JSON response with `"status": "OK"`, `"database": "CONNECTED"`, and real-time grievance statistics.

---

### 5. Nagios Core Phase (Continuous Monitoring)
- [ ] **Screenshot 16: Nagios Core Web Dashboard (Tactical Overview)**
  - URL: `http://43.205.213.123/nagios/`
  - Content: Nagios Tactical Overview showing Hosts: 100% UP, Services: 100% OK.
- [ ] **Screenshot 17: Nagios Service Status Grid for Target Web Server**
  - Location: Navigation menu > Services (or `http://43.205.213.123/nagios/cgi-bin/status.cgi?host=web-target.devops.lab`)
  - Content: Showing all 4 services green (`OK`):
    1. `ICMP Ping` -> `PING OK - Packet loss = 0%, RTA = 0.25 ms`
    2. `HTTP Web Service` -> `HTTP OK: HTTP/1.1 200 OK`
    3. `Grievance App and DB Health` -> `HTTP OK: HTTP/1.1 200 OK - string 'CONNECTED' found`
    4. `CampusVoice Portal UI` -> `HTTP OK: HTTP/1.1 200 OK - string 'CampusVoice' found`

---

### 6. Automated Verification Report
- [ ] **Screenshot 18: End-to-End PowerShell Automated Verification Script**
  - Node: Local Windows Machine
  - Command: `.\scripts\04_verify_all.ps1`
  - Content: All 5 test phases returning green `[PASS]` marks with 100% success rate.
