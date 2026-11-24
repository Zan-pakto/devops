output "vpc_id" {
  description = "ID of the created VPC"
  value       = aws_vpc.main.id
}

output "subnet_id" {
  description = "ID of the public subnet"
  value       = aws_subnet.public.id
}

output "mgmt_server_instance_id" {
  description = "EC2 Instance ID of the Management Server"
  value       = aws_instance.mgmt_server.id
}

output "mgmt_server_public_ip" {
  description = "Public IP address of Management Server"
  value       = aws_instance.mgmt_server.public_ip
}

output "mgmt_server_private_ip" {
  description = "Private IP address of Management Server"
  value       = aws_instance.mgmt_server.private_ip
}

output "target_server_instance_id" {
  description = "EC2 Instance ID of the Target Web Server"
  value       = aws_instance.target_server.id
}

output "target_server_public_ip" {
  description = "Public IP address of Target Web Server"
  value       = aws_instance.target_server.public_ip
}

output "target_server_private_ip" {
  description = "Private IP address of Target Web Server"
  value       = aws_instance.target_server.private_ip
}

output "ssh_mgmt_command" {
  description = "SSH Command to connect to Management Server"
  value       = "ssh -i D:/DevOps-Project/keys/devops-key.pem ubuntu@${aws_instance.mgmt_server.public_ip}"
}

output "ssh_target_command" {
  description = "SSH Command to connect to Target Web Server"
  value       = "ssh -i D:/DevOps-Project/keys/devops-key.pem ubuntu@${aws_instance.target_server.public_ip}"
}

output "website_url" {
  description = "URL of the deployed website on Target Web Server"
  value       = "http://${aws_instance.target_server.public_ip}"
}

output "nagios_dashboard_url" {
  description = "URL of Nagios Core Dashboard on Management Server"
  value       = "http://${aws_instance.mgmt_server.public_ip}/nagios"
}
