# System Architecture & Technical Design

## 1. Architectural Blueprint

The architecture is engineered to fulfill the practical examination requirements of an automated cloud deployment, configuration management, and monitoring pipeline.

### Topology Overview
- **Region:** AWS Asia Pacific (Mumbai) — `ap-south-1`
- **Virtual Private Cloud (VPC):** `10.0.0.0/16`
- **Subnet:** Single Public Subnet `10.0.1.0/24` in `ap-south-1a`
- **Internet Gateway:** Attached to VPC to enable external ingress and egress
- **Route Table:** `0.0.0.0/0` routed to the Internet Gateway

```
+-----------------------------------------------------------------------------------------+
|                                    AWS Cloud: ap-south-1                                 |
|                                                                                         |
|  [VPC: 10.0.0.0/16]                                                                     |
|  +-----------------------------------------------------------------------------------+  |
|  | [Public Subnet: 10.0.1.0/24]                                                      |  |
|  |                                                                                   |  |
|  |  +-------------------------------------+   +-----------------------------------+  |  |
|  |  | Management Node                     |   | Target Web Server Node            |  |  |
|  |  | Hostname: puppet-mgmt.devops.lab    |   | Hostname: web-target.devops.lab   |  |  |
|  |  | Private IP: 10.0.1.10               |   | Private IP: 10.0.1.20             |  |  |
|  |  | OS: Ubuntu 22.04 LTS (Jammy)        |   | OS: Ubuntu 22.04 LTS (Jammy)      |  |  |
|  |  | Sizing: t3.small (2 vCPU, 2GB RAM)  |   | Sizing: t3.micro (2 vCPU, 1GB RAM)|  |  |
|  |  +-------------------------------------+   +-----------------------------------+  |  |
|  |  | Components:                         |   | Components:                       |  |  |
|  |  |  1. Puppet Server 7.x (Port 8140)   |   |  1. Puppet Agent 7.x              |  |  |
|  |  |  2. Ansible Control Node            |   |  2. Apache 2.4 (Port 80)          |  |  |
|  |  |  3. Nagios Core 4.x (Port 80/nagios)|   |  3. Responsive Showcase Site      |  |  |
|  |  +-------------------------------------+   +-----------------------------------+  |  |
|  +-----------------------------------------------------------------------------------+  |
+-----------------------------------------------------------------------------------------+
```

---

## 2. Security & Firewall Architecture (Least Privilege)

### Security Group 1: Management Server (`devops-college-project-mgmt-sg`)
| Direction | Protocol | Port Range | Source / Destination | Purpose |
|---|---|---|---|---|
| Inbound | TCP | 22 | Administrator Public IP (`38.183.62.131/32`) | Secure Admin SSH access |
| Inbound | TCP | 80 | Administrator Public IP (`38.183.62.131/32`) | Nagios Core Web UI access |
| Inbound | TCP | 8140 | VPC Subnet (`10.0.1.0/24`) | Puppet Master SSL catalog communication |
| Outbound | ALL | 0-65535 | `0.0.0.0/0` | OS package updates & package downloads |

### Security Group 2: Target Web Server (`devops-college-project-target-sg`)
| Direction | Protocol | Port Range | Source / Destination | Purpose |
|---|---|---|---|---|
| Inbound | TCP | 80 | `0.0.0.0/0` | Public HTTP access for website demo |
| Inbound | TCP | 22 | Administrator Public IP (`38.183.62.131/32`) | Direct emergency SSH access |
| Inbound | TCP | 22 | Management SG (`mgmt-sg`) | Ansible SSH deployment execution |
| Inbound | ICMP | Echo (-1) | Management SG (`mgmt-sg`) | Nagios host alive ping checks |
| Outbound | ALL | 0-65535 | `0.0.0.0/0` | OS package updates & package downloads |

---

## 3. DevOps Lifecycle Implementation Mapping

### Step 1: Infrastructure as Code (Terraform)
- **Role:** Deterministic provisioning of all AWS infrastructure.
- **Provider:** HashiCorp AWS Provider v5.x.
- **Artifacts:** `main.tf`, `variables.tf`, `outputs.tf`, `providers.tf`.

### Step 2: Configuration Management (Puppet)
- **Role:** Ensuring target web server configuration is maintained in its desired state.
- **Master Node:** `puppet-mgmt.devops.lab` (10.0.1.10).
- **Agent Node:** `web-target.devops.lab` (10.0.1.20).
- **Artifacts:** `puppet/manifests/site.pp`, `puppet/modules/apache_web/manifests/init.pp`.

### Step 3: Application Orchestration (Ansible)
- **Role:** Agentless deployment of application source code to the web server.
- **Controller:** Management Server.
- **Target:** Web Target Server.
- **Artifacts:** `ansible/ansible.cfg`, `ansible/inventory.ini`, `ansible/playbook.yml`.

### Step 4: Continuous Health & Performance Monitoring (Nagios Core)
- **Role:** Real-time polling and alerting on host vitality and HTTP service availability.
- **Host Check:** ICMP ping check (`check_ping!100.0,20%!500.0,60%`).
- **Service Check:** Active HTTP port 80 check (`check_http`).
- **Artifacts:** `nagios/hosts.cfg`, `nagios/services.cfg`, `nagios/commands.cfg`.
