<#
.SYNOPSIS
    Automates initial Git commit and push to the remote GitHub repository.
.DESCRIPTION
    Safely initializes git in D:\DevOps-Project, ensures sensitive files are ignored,
    adds the remote origin, and pushes code to https://github.com/Zan-pakto/devops.git.
#>

param(
    [string]$RemoteUrl = "https://github.com/Zan-pakto/devops.git",
    [string]$Branch = "main"
)

$ErrorActionPreference = "Stop"
Set-Location -Path "D:\DevOps-Project"

Write-Host "=== Preparing DevOps Project for GitHub Upload ===" -ForegroundColor Cyan

# 1. Initialize Git repository if needed
if (-not (Test-Path ".git")) {
    Write-Host "[1/5] Initializing local Git repository..." -ForegroundColor Yellow
    git init -b $Branch
} else {
    Write-Host "[1/5] Git repository already initialized." -ForegroundColor Green
    git branch -M $Branch
}

# 2. Safety check: ensure keys are not staged
Write-Host "[2/5] Staging files adhering to .gitignore..." -ForegroundColor Yellow
git add .

$stagedKeys = git diff --cached --name-only | Select-String -Pattern "\.pem$|\.key$|terraform\.tfvars$"
if ($stagedKeys) {
    Write-Host "SECURITY ERROR: Sensitive files detected in staging area:" -ForegroundColor Red
    $stagedKeys | ForEach-Object { Write-Host " - $_" -ForegroundColor Red }
    git reset
    exit 1
}

# 3. Commit files
Write-Host "[3/5] Creating initial commit..." -ForegroundColor Yellow
git commit -m "feat: complete automated cloud infrastructure, configuration management, application deployment and monitoring pipeline"

# 4. Configure remote origin
Write-Host "[4/5] Setting remote origin to $RemoteUrl..." -ForegroundColor Yellow
$currentRemote = git remote get-url origin 2>$null
if ($null -eq $currentRemote) {
    git remote add origin $RemoteUrl
} else {
    git remote set-url origin $RemoteUrl
}

# 5. Push to GitHub
Write-Host "[5/5] Ready to push to GitHub repository!" -ForegroundColor Green
Write-Host "Run the following command to complete the push:" -ForegroundColor Cyan
Write-Host "  git push -u origin $Branch" -ForegroundColor White
