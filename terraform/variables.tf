variable "aws_region" {
  description = "AWS Region for deployment"
  type        = string
  default     = "ap-south-1"
}

variable "aws_profile" {
  description = "AWS CLI profile name"
  type        = string
  default     = "terraform-user"
}

variable "project_name" {
  description = "Project resource name prefix"
  type        = string
  default     = "devops-college-project"
}

variable "vpc_cidr" {
  description = "CIDR block for the dedicated VPC"
  type        = string
  default     = "10.0.0.0/16"
}

variable "subnet_cidr" {
  description = "CIDR block for the public subnet"
  type        = string
  default     = "10.0.1.0/24"
}

variable "my_ip" {
  description = "Public IP of the administrator workstation for secure SSH/management access"
  type        = string
  default     = "38.183.62.131/32"
}

variable "mgmt_instance_type" {
  description = "Instance type for Management Server (Puppet Server, Ansible, Nagios)"
  type        = string
  default     = "t3.small"
}

variable "target_instance_type" {
  description = "Instance type for Target Web Server (Puppet Agent, Apache)"
  type        = string
  default     = "t3.micro"
}

variable "public_key_path" {
  description = "Local filesystem path to SSH public key"
  type        = string
  default     = "D:/DevOps-Project/keys/devops-key.pub"
}
