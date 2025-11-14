# GitHub SSH Key Setup Guide

## Problem
```
git@github.com: Permission denied (publickey).
fatal: Could not read from remote repository.
```

## Solution 1: Add SSH Key to GitHub (Recommended)

### Step 1: Copy Your SSH Public Key
Your SSH public key has been copied to clipboard. If not, run:
```powershell
Get-Content ~/.ssh/id_rsa.pub | Set-Clipboard
```

### Step 2: Add Key to GitHub
1. Go to GitHub: https://github.com/settings/keys
2. Click **"New SSH key"**
3. Title: Give it a name (e.g., "Windows PC" or "Cloudways Server")
4. Key: Paste your SSH public key (from clipboard)
5. Click **"Add SSH key"**

### Step 3: Test Connection
```powershell
ssh -T git@github.com
```
You should see: "Hi Zahrah-wasif1! You've successfully authenticated..."

### Step 4: Try Git Pull Again
```powershell
git pull origin main
```

---

## Solution 2: Use HTTPS Instead (Quick Fix)

If you don't want to set up SSH, switch to HTTPS:

```powershell
git remote set-url origin https://github.com/Zahrah-wasif1/chat-php.git
git pull origin main
```

You'll be prompted for GitHub username and password (or Personal Access Token).

---

## Your SSH Public Key
```
ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAACAQCbpkJ9ZBwIF7plWGDp5yURbTTOulqrxi5L1oAgcOLFygPgYwqA7l+xxW4PkrgYLqWsBh0yy0SxKmUOY4+zRWG2lRXykup2Y4/d7DxFhVrruxf+U3U8HGGpP2wqMOG9RLon054nFMp34Vlzy2qiy5yYbOH38nJLKyprJLpfB3USINnByomfwPfM0qD6NVZRavp4k7mLRrtsEAfxscgNaGeiHumoMSeq1QCrZbrmpkkA7jnc5lo3Sd2UkRN7wwJQR5Zk7WaIKIlpB6ckC+7BWh2hxucQA6oyfwMIBaxSGxryFC6Losg7vj7uKZlzMYZ+kQkN+0x/nbWc4CJ8WrfODbHGx/+XafCZgmIuQtg6dpJ+ZcSE8lHPCeUsHxwKLOLBqI40ZF00vEhCC0va2w+S3MbIo2RlvNJDIIEHPY1yayphyc6kMTDAUFfy/vg7JIVnsCCfejgmq5baCwVIG5NmAgYlSjluERDixLfRtUow2u/gfCo+1Fk0bzCl4v5/2tojXfXLXRJW7GaBCsKswBZhMR0J87CcoNQ5m6teMENr2sAlEZpGwKZZcxSy8yPcn+LU9udyroQLHmQWLqiJCqzl7WawXUZd4HB6yREjBlLDENRvlrUmt+kSBnkycrKEh/VnkVJH1bfE+xaukckFUIyViLrQoW8qIDHcYWHOLYBMHc0TrQ== zahrahwasif@gmail.com
```

Copy this key and add it to GitHub at: https://github.com/settings/keys

