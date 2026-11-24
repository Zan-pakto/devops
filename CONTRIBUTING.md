# Guidelines for Project Submission & Evaluation

## Project Verification
1. Ensure AWS credentials are configured via standard AWS CLI profiles.
2. Never commit private key files (`.pem`) or active `terraform.tfvars` containing public IP addresses.
3. Use `terraform.tfvars.example` as a template for new deployments.

## Code Standards
- Terraform files must follow standard formatting (`terraform fmt`).
- Manifests and playbooks should remain idempotent and self-contained.
- All web application endpoints must return valid HTTP status codes and pass automated smoke tests.
