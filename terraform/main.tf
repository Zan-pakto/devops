# Query latest Ubuntu 22.04 LTS (Jammy) AMI in ap-south-1
data "aws_ami" "ubuntu" {
  most_recent = true
  owners      = ["099720109477"] # Canonical

  filter {
    name   = "name"
    values = ["ubuntu/images/hvm-ssd/ubuntu-jammy-22.04-amd64-server-*"]
  }

  filter {
    name   = "virtualization-type"
    values = ["hvm"]
  }
}

# AWS Key Pair
resource "aws_key_pair" "devops_key" {
  key_name   = "${var.project_name}-key"
  public_key = file(var.public_key_path)

  tags = {
    Name    = "${var.project_name}-key"
    Project = var.project_name
  }
}

# Dedicated VPC
resource "aws_vpc" "main" {
  cidr_block           = var.vpc_cidr
  enable_dns_hostnames = true
  enable_dns_support   = true

  tags = {
    Name    = "${var.project_name}-vpc"
    Project = var.project_name
  }
}

# Internet Gateway
resource "aws_internet_gateway" "gw" {
  vpc_id = aws_vpc.main.id

  tags = {
    Name    = "${var.project_name}-igw"
    Project = var.project_name
  }
}

# Public Subnet
resource "aws_subnet" "public" {
  vpc_id                  = aws_vpc.main.id
  cidr_block              = var.subnet_cidr
  availability_zone       = "${var.aws_region}a"
  map_public_ip_on_launch = true

  tags = {
    Name    = "${var.project_name}-public-subnet"
    Project = var.project_name
  }
}

# Route Table for Public Subnet
resource "aws_route_table" "public" {
  vpc_id = aws_vpc.main.id

  route {
    cidr_block = "0.0.0.0/0"
    gateway_id = aws_internet_gateway.gw.id
  }

  tags = {
    Name    = "${var.project_name}-public-rt"
    Project = var.project_name
  }
}

# Route Table Association
resource "aws_route_table_association" "public" {
  subnet_id      = aws_subnet.public.id
  route_table_id = aws_route_table.public.id
}

# -------------------------------------------------------------
# Security Group: Management Server
# -------------------------------------------------------------
resource "aws_security_group" "mgmt_sg" {
  name        = "${var.project_name}-mgmt-sg"
  description = "Security Group for DevOps Management Server"
  vpc_id      = aws_vpc.main.id

  # SSH restricted strictly to administrator workstation IP
  ingress {
    description = "SSH from Admin Workstation"
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = [var.my_ip]
  }

  # Nagios Web UI restricted strictly to administrator workstation IP
  ingress {
    description = "Nagios Web Dashboard from Admin Workstation"
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = [var.my_ip]
  }

  # Puppet Server (8140) restricted strictly to internal subnet
  ingress {
    description = "Puppet Master port from VPC subnet"
    from_port   = 8140
    to_port     = 8140
    protocol    = "tcp"
    cidr_blocks = [var.subnet_cidr]
  }

  # Outbound egress to internet
  egress {
    description = "Allow all outbound traffic"
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name    = "${var.project_name}-mgmt-sg"
    Project = var.project_name
  }
}

# -------------------------------------------------------------
# Security Group: Target Web Server
# -------------------------------------------------------------
resource "aws_security_group" "target_sg" {
  name        = "${var.project_name}-target-sg"
  description = "Security Group for Target Web Server"
  vpc_id      = aws_vpc.main.id

  # HTTP port 80 public access for website demonstration
  ingress {
    description = "Public HTTP Web Traffic"
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  # SSH from Admin Workstation
  ingress {
    description = "SSH from Admin Workstation"
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = [var.my_ip]
  }

  # SSH from Management Server (for Ansible deployment)
  ingress {
    description     = "SSH from Management Server"
    from_port       = 22
    to_port         = 22
    protocol        = "tcp"
    security_groups = [aws_security_group.mgmt_sg.id]
  }

  # ICMP Ping from Management Server (for Nagios host check)
  ingress {
    description     = "ICMP Ping from Management Server"
    from_port       = -1
    to_port         = -1
    protocol        = "icmp"
    security_groups = [aws_security_group.mgmt_sg.id]
  }

  # Outbound egress
  egress {
    description = "Allow all outbound traffic"
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name    = "${var.project_name}-target-sg"
    Project = var.project_name
  }
}

# -------------------------------------------------------------
# EC2: Management Server (Puppet Server + Ansible + Nagios)
# -------------------------------------------------------------
resource "aws_instance" "mgmt_server" {
  ami                         = data.aws_ami.ubuntu.id
  instance_type               = var.mgmt_instance_type
  subnet_id                   = aws_subnet.public.id
  vpc_security_group_ids      = [aws_security_group.mgmt_sg.id]
  key_name                    = aws_key_pair.devops_key.key_name
  private_ip                  = "10.0.1.10"
  associate_public_ip_address = true

  root_block_device {
    volume_size           = 20
    volume_type           = "gp3"
    delete_on_termination = true
  }

  user_data = <<-EOF
              #!/bin/bash
              hostnamectl set-hostname puppet-mgmt.devops.lab
              echo "10.0.1.10 puppet-mgmt.devops.lab puppet" >> /etc/hosts
              echo "10.0.1.20 web-target.devops.lab web-target" >> /etc/hosts
              fallocate -l 2G /swapfile
              chmod 600 /swapfile
              mkswap /swapfile
              swapon /swapfile
              echo '/swapfile none swap sw 0 0' >> /etc/fstab
              EOF

  tags = {
    Name    = "${var.project_name}-mgmt-server"
    Role    = "Management"
    Project = var.project_name
  }
}

# -------------------------------------------------------------
# EC2: Target Web Server (Puppet Agent + Apache)
# -------------------------------------------------------------
resource "aws_instance" "target_server" {
  ami                         = data.aws_ami.ubuntu.id
  instance_type               = var.target_instance_type
  subnet_id                   = aws_subnet.public.id
  vpc_security_group_ids      = [aws_security_group.target_sg.id]
  key_name                    = aws_key_pair.devops_key.key_name
  private_ip                  = "10.0.1.20"
  associate_public_ip_address = true

  root_block_device {
    volume_size           = 10
    volume_type           = "gp3"
    delete_on_termination = true
  }

  user_data = <<-EOF
              #!/bin/bash
              hostnamectl set-hostname web-target.devops.lab
              echo "10.0.1.10 puppet-mgmt.devops.lab puppet" >> /etc/hosts
              echo "10.0.1.20 web-target.devops.lab web-target" >> /etc/hosts
              EOF

  tags = {
    Name    = "${var.project_name}-target-server"
    Role    = "TargetWebServer"
    Project = var.project_name
  }
}
