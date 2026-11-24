# DevOps College Practical Project: Complete Testing & Verification Report

**Project Title:** Automated Cloud Infrastructure Provisioning, Configuration Management, Web Server Deployment and Monitoring  
**Application Deployed:** **CampusVoice — Real-World Student Complaint & Grievance Redressal Management System**  
**Target Platform:** Amazon Web Services (AWS `ap-south-1` — Mumbai)  
**Execution Date:** Mon Nov 23 2026  
**Final Result:** **100% PASS (ALL PHASES FULLY OPERATIONAL)**  

---

## 1. Executive Test Dashboard

| Tool / Layer | Objective | Validation Method | Measured Result | Status |
|---|---|---|---|---|
| **AWS CLI & IAM** | Programmatic access with non-root IAM user | `aws sts get-caller-identity` | `arn:aws:iam::347204346668:user/terraform-user` | **PASS** |
| **Terraform** | IaC provisioning of VPC, Subnet, SGs, Key Pair, EC2 | `terraform apply` | 10 AWS resources created, 2 SGs updated for current IP | **PASS** |
| **SSH Security** | 4096-bit RSA key pair & NTFS permission lock | `icacls devops-key.pem` | User-exclusive read access, batch auth verified | **PASS** |
| **Puppet Master** | Puppet Server daemon on port 8140 | `systemctl status puppetserver` | Active & running (512M-1G tuned heap + 2GB swap) | **PASS** |
| **Puppet Agent** | Desired state enforcement of Apache2 + PHP 8.1 + SQLite | `puppet agent -t` | Catalog compiled, packages installed & data dir created | **PASS** |
| **Ansible** | Orchestration of CampusVoice web app & SQLite schema | `ansible-playbook` | `PLAY RECAP: ok=12 changed=4 failed=0` | **PASS** |
| **Student Grievance Portal**| Serving interactive grievance submission UI on port 80 | `Invoke-WebRequest http://13.207.51.84` | HTTP 200 OK (`Apache/2.4.52 (Ubuntu)` + PHP 8.1) | **PASS** |
| **Grievance Submission API** | Real-time complaint registration & Ticket generation | `POST http://13.207.51.84/api.php` | Returned Ticket ID `GRV-2026-3399` with status `Pending` | **PASS** |
| **Grievance Tracking API** | Search & timeline resolution lookup | `GET http://13.207.51.84/api.php?action=track` | Returned student record, status & admin remarks | **PASS** |
| **App & DB Health API** | Real-time health & telemetry JSON endpoint | `GET http://13.207.51.84/health.php` | HTTP 200 OK (`{"status":"OK","database":"CONNECTED"}`) | **PASS** |
| **Admin Coordinator Desk** | Review submissions, update status & post remarks | `Invoke-WebRequest http://13.207.51.84/admin.php`| HTTP 200 OK (Workflow desk accessible) | **PASS** |
| **Nagios Core Dashboard** | Web console & Basic Authentication on port 80 | `Invoke-WebRequest http://43.205.213.123/nagios/` | HTTP 200 OK (`nagiosadmin` authenticated) | **PASS** |
| **Nagios Host Check** | ICMP Echo latency & packet loss check | `check_ping` plugin | `PING OK - Packet loss = 0%, RTA = 0.25 ms` | **PASS** |
| **Nagios Web Server** | Continuous HTTP daemon port 80 check | `check_http` plugin | `HTTP OK: HTTP/1.1 200 OK - 12502 bytes in 0.002s` | **PASS** |
| **Nagios App & DB Check** | Deep application & SQLite database health check | Custom `check_app_health` | `HTTP OK: HTTP/1.1 200 OK - string 'CONNECTED' found` | **PASS** |
| **Nagios Portal UI Check**| Student Portal UI availability check | Custom `check_portal_ui` | `HTTP OK: HTTP/1.1 200 OK - string 'CampusVoice' found` | **PASS** |

---

## 2. Infrastructure Inventory & Live Connection Endpoints

- **Management Server (Control Plane):**
  - **Instance ID:** `i-0aeb0f19bd962d2f7`
  - **Public IP:** `43.205.213.123`
  - **Private IP:** `10.0.1.10`
  - **Hostname:** `puppet-mgmt.devops.lab`
  - **Instance Type:** `t3.small` (2 vCPU, 2GB RAM, 2GB Swap)
  - **Services:** Puppet Server 7.17.3 (:8140), Ansible 2.10.8, Nagios Core 4.4.6, Apache 2.4.52
  - **Nagios Dashboard URL:** [http://43.205.213.123/nagios/](http://43.205.213.123/nagios/)
  - **Nagios Credentials:** `nagiosadmin` / `NagiosAdmin2026!`
  - **SSH Command:** `ssh -i D:\DevOps-Project\keys\devops-key.pem ubuntu@43.205.213.123`

- **Target Web Server (Data Plane):**
  - **Instance ID:** `i-0c6368acab1bc4105`
  - **Public IP:** `13.207.51.84`
  - **Private IP:** `10.0.1.20`
  - **Hostname:** `web-target.devops.lab`
  - **Instance Type:** `t3.micro` (2 vCPU, 1GB RAM)
  - **Services:** Puppet Agent 7.34.0, Apache/2.4.52, PHP 8.1.2, SQLite3 PDO
  - **Student Portal URL:** [http://13.207.51.84](http://13.207.51.84)
  - **Track Grievance URL:** [http://13.207.51.84/track.php](http://13.207.51.84/track.php)
  - **Admin Desk URL:** [http://13.207.51.84/admin.php](http://13.207.51.84/admin.php)
  - **Health API URL:** [http://13.207.51.84/health.php](http://13.207.51.84/health.php)
  - **SSH Command:** `ssh -i D:\DevOps-Project\keys\devops-key.pem ubuntu@13.207.51.84`

---

## 3. Component Execution Logs

### A. Puppet State Enforcement (Apache + PHP + SQLite)
```
Info: Using environment 'production'
Notice: Requesting catalog from puppet-mgmt.devops.lab:8140 (10.0.1.10)
Notice: Catalog compiled by puppet-mgmt.devops.lab
Info: Applying configuration version '1795430905'
Notice: Applying Apache Web Configuration via Puppet
Notice: /Stage[main]/Apache_web/Package[php]/ensure: created
Notice: /Stage[main]/Apache_web/Package[libapache2-mod-php]/ensure: created
Notice: /Stage[main]/Apache_web/Package[php-sqlite3]/ensure: created
Notice: /Stage[main]/Apache_web/Package[sqlite3]/ensure: created
Notice: /Stage[main]/Apache_web/File[/var/www/html]/mode: mode changed '0755' to '0775'
Notice: /Stage[main]/Apache_web/File[/var/www/html/data]/ensure: created
Notice: Applied catalog in 57.64 seconds
```

### B. Ansible Application & Database Deployment
```
PLAY [Deploy Real-World Student Complaint Management System (CampusVoice)] *****
TASK [Gathering Facts] ................................................... [OK]
TASK [Verify Apache2 package is installed (managed by Puppet)] ........... [OK]
TASK [Assert Apache2 package presence] ................................... [OK]
TASK [Ensure Apache2 service is active] ................................... [OK]
TASK [Ensure application data directory exists with write permissions] ... [OK]
TASK [Deploy Student Complaint Management System source files] ............ [CHANGED]
TASK [Initialize SQLite database and seed initial records] ............... [CHANGED]
TASK [Ensure SQLite database file permissions for web server] ............ [CHANGED]
TASK [Query health endpoint via local HTTP GET] .......................... [OK (200)]
TASK [Assert application and database health] ............................ [OK]
TASK [Display deployment telemetry summary] .............................. [OK]

PLAY RECAP *********************************************************************
web-target.devops.lab : ok=12 changed=4 unreachable=0 failed=0 skipped=0
```

### C. Live Nagios Monitoring Telemetry
```
service_description=CampusVoice Portal UI
current_state=0
plugin_output=HTTP OK: HTTP/1.1 200 OK - 12502 bytes in 0.004 second response time

service_description=Grievance App and DB Health
current_state=0
plugin_output=HTTP OK: HTTP/1.1 200 OK - 717 bytes in 0.005 second response time

service_description=HTTP Web Service
current_state=0
plugin_output=HTTP OK: HTTP/1.1 200 OK - 12502 bytes in 0.002 second response time

service_description=ICMP Ping
current_state=0
plugin_output=PING OK - Packet loss = 0%, RTA = 0.25 ms
```

### D. Automated Verification Suite Output
```
=====================================================================
 CampusVoice DevOps End-to-End Automated Verification Suite
=====================================================================
Management Server IP (Control Plane) : 43.205.213.123
Target Web Server IP (Data Plane)    : 13.207.51.84

[1/5] Testing Student Grievance Portal (http://13.207.51.84)...
  [PASS] Student Portal is ACTIVE! (HTTP 200 OK)
  [PASS] Web Engine: Apache/2.4.52 (Ubuntu)

[2/5] Testing Application & Database Health API (http://13.207.51.84/health.php)...
  [PASS] Application Status : OK
  [PASS] Database Status    : CONNECTED (SQLite3 PDO)
  [PASS] Total Grievances   : 5 recorded
  [PASS] API Response Time  : 0.47 ms

[3/5] Testing Grievance Tracking Timeline (http://13.207.51.84/track.php?query=GRV-2026-1001)...
  [PASS] Tracking System returned live grievance details & resolution timeline!

[4/5] Testing Admin Grievance Desk (http://13.207.51.84/admin.php)...
  [PASS] Administrative Workflow Desk is ACCESSIBLE! (HTTP 200 OK)

[5/5] Testing Nagios Core Dashboard & 4 Service Checks (http://43.205.213.123/nagios/)...
  [PASS] Nagios Dashboard Authenticated & Active! (HTTP 200 OK)
  [PASS] Active Checks Configured: ICMP Ping, HTTP Daemon, DB Health, Portal UI

=====================================================================
 DEVOPS ARCHITECTURE VERIFICATION SUMMARY
  [PASS] 1. Terraform: Dedicated VPC, Subnet, Route Tables, SGs, Dual EC2 Nodes
  [PASS] 2. Puppet:    Managed Apache2 + PHP 8.1 + SQLite Runtime & Directory State
  [PASS] 3. Ansible:   Deployed CampusVoice App, Initialized DB, Seeded Grievances
  [PASS] 4. Nagios:    Continuously Monitoring Host Vitality, Daemon & DB API (4/4 OK)
=====================================================================
```
