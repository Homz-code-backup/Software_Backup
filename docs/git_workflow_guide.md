# Git Branching Workflow Guide

This guide explains how to work with branches in your ERP v2 project instead of pushing directly to `main`.

## Why Use Branches?

- **Safety**: Keep `main` branch stable and production-ready
- **Organization**: Separate different features or fixes
- **Collaboration**: Multiple developers can work simultaneously
- **Review**: Changes can be reviewed before merging to `main`

---

## Daily Workflow

### 1. Create a New Branch for Your Work

Before starting any new feature or changes, create a new branch:

```bash
# Create and switch to a new branch
git checkout -b feature/your-feature-name

# Examples:
git checkout -b feature/estimate-module
git checkout -b fix/login-bug
git checkout -b update/database-schema
```

**Branch naming conventions:**

- `feature/` - for new features
- `fix/` - for bug fixes
- `update/` - for updates or improvements
- `hotfix/` - for urgent production fixes

### 2. Make Your Changes

Work on your code as usual. When ready to save:

```bash
# Check what files changed
git status

# Add all changes
git add .

# Or add specific files
git add path/to/file.php

# Commit with a descriptive message
git commit -m "Add estimate calculation feature"
```

### 3. Push Your Branch to GitHub

```bash
# First time pushing a new branch
git push -u origin feature/your-feature-name

# Subsequent pushes on the same branch
git push
```

### 4. Keep Your Branch Updated

If `main` branch gets updated, sync your branch:

```bash
# Switch to main and pull latest changes
git checkout main
git pull origin main

# Switch back to your branch
git checkout feature/your-feature-name

# Merge main into your branch
git merge main
```

### 5. Merge to Main (When Ready)

When your feature is complete and tested:

**Option A: Merge Locally**

```bash
# Switch to main
git checkout main

# Pull latest changes
git pull origin main

# Merge your branch
git merge feature/your-feature-name

# Push to GitHub
git push origin main

# Delete the branch (optional)
git branch -d feature/your-feature-name
git push origin --delete feature/your-feature-name
```

**Option B: Create Pull Request on GitHub** (Recommended)

1. Push your branch to GitHub
2. Go to your repository on GitHub
3. Click "Compare & pull request"
4. Review changes and create pull request
5. Merge the pull request on GitHub
6. Pull the updated main locally

---

## Quick Reference Commands

### Branch Management

```bash
# List all branches
git branch -a

# Switch to existing branch
git checkout branch-name

# Create and switch to new branch
git checkout -b new-branch-name

# Delete local branch
git branch -d branch-name

# Delete remote branch
git push origin --delete branch-name
```

### Check Current Branch

```bash
git branch
# The branch with * is your current branch
```

### View Branch History

```bash
git log --oneline --graph --all
```

---

## Recommended Workflow for ERP v2

### For Daily Development Work

1. **Start of day**: Pull latest main

   ```bash
   git checkout main
   git pull origin main
   ```

2. **Create feature branch**

   ```bash
   git checkout -b feature/todays-work
   ```

3. **Work and commit regularly**

   ```bash
   git add .
   git commit -m "Descriptive message"
   ```

4. **Push to backup your work**

   ```bash
   git push -u origin feature/todays-work
   ```

5. **End of day**: If feature is complete, merge to main
   ```bash
   git checkout main
   git merge feature/todays-work
   git push origin main
   ```

### For Long-term Features

Keep the feature branch for days/weeks, push regularly:

```bash
git add .
git commit -m "Progress on feature"
git push
```

Merge to `main` only when fully complete and tested.

---

## Current Setup

Your repository is currently on the `main` branch. To start using branches:

```bash
# Create your first development branch
git checkout -b development

# Now you're on the 'development' branch
# Make changes, commit, and push
git push -u origin development
```

---

## Tips

> [!TIP]
>
> - Commit often with clear messages
> - Push your branch daily to backup your work
> - Keep branch names short and descriptive
> - Delete merged branches to keep repository clean

> [!IMPORTANT]
>
> - Always pull `main` before creating a new branch
> - Test your changes before merging to `main`
> - Never force push (`git push -f`) unless absolutely necessary

> [!WARNING]
>
> - Don't work directly on `main` branch
> - Don't commit sensitive data (passwords, API keys)
> - Don't commit large binary files unnecessarily
