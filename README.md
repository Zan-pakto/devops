# CampusVoice: Automated Cloud Infrastructure Provisioning, Configuration Management, Real-World Web Application Deployment & Telemetry Monitoring

**College Practical Capstone Project**  
*Application:* **CampusVoice — Student Complaint & Grievance Redressal Management System**  
*Technology Stack:* **Terraform, Puppet, Ansible, Nagios Core, Ubuntu Linux, Apache2, PHP 8.1, SQLite3 PDO**  
*Cloud Platform:* **Amazon Web Services (AWS) — Region: ap-south-1 (Mumbai)**  

---

## 📌 Project Overview

This project implements an enterprise-grade automated DevOps lifecycle deploying a real-world, dynamic **Student Complaint & Grievance Redressal Management System** (`CampusVoice`) across two dedicated AWS EC2 instances:
1. **Terraform (Infrastructure as Code):** Provisions an isolated AWS Virtual Private Cloud (10.0.0.0/16), public subnet (10.0.1.0/24), Internet Gateway, strict perimeter Security Groups, 4096-bit RSA SSH key pair, and two Ubuntu 22.04 LTS EC2 instances with deterministic private IPs.
2. **Puppet (Configuration Management):** Enforces desired system state on the target node. Configures Apache2, installs PHP 8.1 runtime with SQLite extensions (`libapache2-mod-php`, `php-sqlite3`, `sqlite3`), manages document root permissions, and ensures service persistence.
3. **Ansible (Application Orchestration):** Deploys the full-stack Student Complaint Management web application (`index.php`, `track.php`, `admin.php`, `api.php`, `db.php`, `health.php`), creates the SQLite database schema, seeds realistic campus grievances, hardens permissions (`www-data:www-data`), and performs post-deployment automated smoke tests.
4. **Nagios Core (Continuous Telemetry Monitoring):** Real-time NOC monitoring from the Management Server with 4 active service checks: Host Vitality (ICMP Ping), Web Server Daemon (HTTP Port 80), Deep Database Health (`/health.php` asserting database connectivity), and Portal UI Availability (`/index.php` asserting `CampusVoice`).

---

## 🌐 Live Access Endpoints & Credentials

| Service | Access URL / Command | Credentials / Notes | Status |
|---|---|---|---|
| **Student Grievance Portal** | [http://13.207.51.84](http://13.207.51.84) | Public HTTP (Port 80) &bull; Lodge complaints & get instant ticket IDs | **ACTIVE (HTTP 200)** |
| **Grievance Tracking Portal** | [http://13.207.51.84/track.php](http://13.207.51.84/track.php) | Enter Ticket ID (e.g. `GRV-2026-1001`) or Roll No (e.g. `22CS104`) | **ACTIVE (HTTP 200)** |
| **Admin Coordinator Desk** | [http://13.207.51.84/admin.php](http://13.207.51.84/admin.php) | Manage grievances, filter, update status & post resolution notes | **ACTIVE (HTTP 200)** |
| **Application Health API** | [http://13.207.51.84/health.php](http://13.207.51.84/health.php) | Returns real-time JSON metrics & database connectivity status | **ACTIVE (HTTP 200)** |
| **Nagios Core Dashboard** | [http://43.205.213.123/nagios/](http://43.205.213.123/nagios/) | Username: `nagiosadmin`<br>Password: `NagiosAdmin2026!` | **ACTIVE (HTTP 200)** |
| **Management Server SSH** | `ssh -i D:\DevOps-Project\keys\devops-key.pem ubuntu@43.205.213.123` | Puppet Server, Ansible Controller, Nagios Core | **CONNECTED** |
| **Target Web Server SSH** | `ssh -i D:\DevOps-Project\keys\devops-key.pem ubuntu@13.207.51.84` | Puppet Agent, Apache2 Web Server, SQLite DB | **CONNECTED** |

---

## 🏗️ Multi-Tier Architecture Diagram

```
                       +----------------------------------------+
                       |       Administrator Workstation         |
                       |       (Public IP: 210.89.61.42)        |
                       +----------------------------------------+
                                    |              |
                      SSH Port 22   |              |  HTTP Port 80 (Student Web & Admin Desk)
                   Nagios UI Port 80|              |
                                    v              v
+-----------------------------------------------------------------------------------------+
| AWS ap-south-1 | Dedicated VPC (10.0.0.0/16) | Public Subnet (10.0.1.0/24)               |
|                                                                                         |
|   +------------------------------------+   +------------------------------------+       |
|   |   DevOps Management Server         |   |        Target Web Server           |       |
|   |   (puppet-mgmt.devops.lab)         |   |      (web-target.devops.lab)       |       |
|   |   Private IP: 10.0.1.10            |   |      Private IP: 10.0.1.20         |       |
|   |   Public IP:  43.205.213.123       |   |      Public IP:  13.207.51.84      |       |
|   |   Instance: t3.small (2GB RAM)     |   |      Instance: t3.micro (1GB RAM)  |       |
|   +------------------------------------+   +------------------------------------+       |
|   | - Puppet Server 7.x (:8140)        |   | - Puppet Agent 7.x                 |       |
|   | - Ansible Control Node             |---| - Apache/2.4 + PHP 8.1 Runtime     |       |
|   | - Nagios Core 4.x Monitoring       |   | - CampusVoice Complaint System     |       |
|   | - Apache2 (Nagios Web Dashboard)   |   | - SQLite3 Database Backend         |       |
|   +------------------------------------+   +------------------------------------+       |
|                   |                                     ^                               |
|                   |==== 1. Puppet Catalog & Desired State Config (Apache+PHP) ==|       |
|                   |==== 2. Ansible SSH Playbook Runs (Deploy App & DB Schema) ==|       |
|                   |==== 3. Nagios ICMP Ping Check (Host Vitality) ==============|       |
|                   |==== 4. Nagios HTTP Daemon Check (Port 80) ==================|       |
|                   |==== 5. Nagios App & DB Health API Check (/health.php) ======|       |
|                   |==== 6. Nagios Student Portal UI Check (/index.php) =========|       |
+-----------------------------------------------------------------------------------------+
```

---

## 📂 Project Repository Structure

```
D:\DevOps-Project\
├── README.md                           # Master Project Guide & Architecture
├── .gitignore                          # Protects keys, credentials & Terraform state
├── keys/
│   ├── devops-key.pem                  # 4096-bit RSA Private Key (NTFS ACL restricted)
│   └── devops-key.pub                  # SSH Public Key for AWS EC2 Key Pair
├── terraform/
│   ├── providers.tf                    # AWS Provider & Version Constraints
│   ├── variables.tf                    # VPC, Subnet, Sizing & IP Variables
│   ├── terraform.tfvars                # Variable Values for ap-south-1
│   ├── main.tf                         # VPC, Subnet, SGs, Key Pair & Dual EC2 Instances
│   └── outputs.tf                      # Public IPs, URLs & Connection Strings
├── puppet/
│   ├── manifests/
│   │   └── site.pp                     # Puppet Node Declaration for web-target
│   └── modules/
│       └── apache_web/
│           └── manifests/
│               └── init.pp             # Apache2, PHP 8.1, SQLite3 & Directory Manifest
├── ansible/
│   ├── ansible.cfg                     # Ansible Inventory & SSH Settings
│   ├── inventory.ini                   # Target Host Definition (10.0.1.20)
│   └── playbook.yml                    # Deploys CampusVoice Web App, DB & Smoke Tests
├── website/                            # Real-World Student Complaint Management System
│   ├── index.php                       # Student Grievance Submission Portal & Metrics
│   ├── track.php                       # Grievance Timeline Tracking System
│   ├── admin.php                       # Administrative Coordinator Redressal Desk
│   ├── api.php                         # RESTful API for CRUD Grievance Operations
│   ├── db.php                          # SQLite3 PDO Database Initialization & Seeds
│   ├── health.php                      # Application & Database Telemetry Health Check
│   ├── style.css                       # Responsive UI Design & Status Badges
│   └── script.js                       # Interactive AJAX Logic & Modal System
├── nagios/
│   ├── hosts.cfg                       # Target Host Definition (web-target.devops.lab)
│   ├── services.cfg                    # 4 Active Service Check Definitions
│   ├── commands.cfg                    # Custom Check Commands for Health & UI
│   └── setup_nagios.sh                 # Nagios Core Automated Setup Script
├── scripts/
│   ├── 01_setup_management.sh          # Management Server Setup (Puppet, Ansible, Nagios)
│   ├── 02_setup_puppet_agent.sh        # Target Agent Setup & CSR Submission
│   ├── 03_run_ansible.sh               # Runs Ansible Playbook
│   └── 04_verify_all.ps1               # Automated 5-Stage PowerShell Test Suite
└── docs/
    ├── ARCHITECTURE.md                 # Deep Architectural Breakdown
    ├── COMMAND_REFERENCE.md            # Complete CLI Command Reference
    ├── SCREENSHOTS_GUIDE.md            # Guide for College Lab Report Images
    └── TESTING_REPORT.md               # 100% Passing Test Results & Telemetry Logs
```
