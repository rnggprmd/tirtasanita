# 🧹 Git Cleanup - Remove Secrets from History

**Issue:** Old commits contain Midtrans secrets  
**Status:** ⚠️ NEEDS CLEANUP  
**Solution:** Use git filter-branch or amend

---

## 🔍 Problem Identified

GitHub detected Midtrans secrets in these commits:
- `b1232d0` - GIT_PUSH_FIX.md with example keys
- `1bcaa4f` - MIDTRANS_SETUP.md with actual keys  
- `config/midtrans.php` - With actual credentials

---

## ✅ Solution: Complete History Reset

### **Option 1: Force Push with Clean History (RECOMMENDED)**

This will rewrite history to remove secrets:

#### **Step 1: Reset to clean commit**
```bash
# Go back to the last known good commit
git reset --hard 8af18ec

# This removes all commits after the initial clean state
# (b1232d0, ef2d4f7, 1bcaa4f, 5994919, eee9f61)
```

#### **Step 2: Add fixed files**
```bash
# Now add your fixed files
git add -A
```

#### **Step 3: Commit clean version**
```bash
git commit -m "docs: remove secrets and add environment templates"
```

#### **Step 4: Force push**
```bash
git push origin main --force
```

⚠️ **WARNING:** `--force` rewrites history. Only do this if no one else is working on main.

---

### **Option 2: Manual Secret Removal**

If you want to keep some commits:

#### **Step 1: Remove secrets from files**
Already done:
- ✅ MIDTRANS_SETUP.md - Fixed
- ✅ GIT_PUSH_FIX.md - Fixed  
- ✅ config/midtrans.php - Fixed
- ✅ .env.local.example - Fixed

#### **Step 2: Amend current commit**
```bash
# Stage the fixed files
git add MIDTRANS_SETUP.md GIT_PUSH_FIX.md config/midtrans.php

# Amend to current commit
git commit --amend --no-edit

# Force push
git push origin main --force
```

---

## 🚀 Quick Fix (Recommended)

**Run these commands:**

```bash
# 1. Go to project directory
cd C:\laragon\www\tirtasanita

# 2. Reset to clean state (before secrets)
git reset --hard 8af18ec

# 3. Re-add all current files (clean versions)
git add -A

# 4. Commit with message
git commit -m "docs: remove secrets and add environment templates"

# 5. Force push to GitHub
git push origin main --force
```

---

## ✨ After Cleanup

GitHub will:
- ✅ Accept the push
- ✅ Remove old commits with secrets
- ✅ Keep your clean version
- ✅ Secrets no longer in history

---

## 🔐 Verify Cleanup

After push, verify on GitHub:

1. Go to: https://github.com/rnggprmd/tirtasanita
2. Check main branch
3. Latest commit should be the clean one
4. No more secret warnings

---

## 📝 What's Clean Now

✅ **MIDTRANS_SETUP.md** - Credentials replaced with placeholders  
✅ **GIT_PUSH_FIX.md** - No example keys shown  
✅ **config/midtrans.php** - Template with placeholders  
✅ **.env.local.example** - Template only  
✅ **.gitignore** - Excludes sensitive files  

---

## 🎯 Next Steps

1. **Run cleanup commands** (see Quick Fix section)
2. **Wait for GitHub to process**
3. **Verify no warnings**
4. **Repo is now clean!**

---

**Status:** Ready to cleanup  
**Commands prepared:** Yes  
**Next action:** Run the 5 commands in Quick Fix section

