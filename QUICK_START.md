# 🚀 Quick Start Guide - Tirta Sanita Outbound

Panduan cepat untuk setup dan menjalankan project dalam 5 menit.

---

## ⚡ Setup Tercepat (5 Menit)

### **1. Dengan Laragon** ✅ RECOMMENDED

```bash
# Clone project
cd C:\laragon\www
git clone https://github.com/username/tirtasanita.git
cd tirtasanita

# Install dependencies
composer install

# Import database (gunakan phpMyAdmin: http://localhost/phpmyadmin)
# Database name: tirtasanita_db
# File: database/tirtasanita_db.sql

# Start Laragon
# Click "Start All" di Laragon Control Panel

# Open browser
# Homepage: http://localhost/tirtasanita
# Admin: http://localhost/tirtasanita/admin
```

### **2. Dengan XAMPP**

```bash
# Copy project ke htdocs
cd C:\xampp\htdocs
git clone https://github.com/username/tirtasanita.git
cd tirtasanita

# Install dependencies
composer install

# Start XAMPP (Apache + MySQL)
# Open phpMyAdmin: http://localhost/phpmyadmin
# Create database: tirtasanita_db
# Import SQL file: database/tirtasanita_db.sql

# Open browser
# Homepage: http://localhost/tirtasanita
# Admin: http://localhost/tirtasanita/admin
```

---

## 🔑 Default Login

**Admin Panel:** http://localhost/tirtasanita/admin

```
WhatsApp: 0812345678910
Password: admin123
```

⚠️ **Ingat: Ubah password ini setelah login pertama kali!**

---

## 📋 Checklist Setup

### ✅ Installation
- [ ] Clone/download project
- [ ] Run `composer install`
- [ ] Create database `tirtasanita_db`
- [ ] Import `database/tirtasanita_db.sql`
- [ ] Start web server (Laragon/XAMPP)

### ✅ Configuration
- [ ] Create `config/midtrans.php` (copy `config/midtrans.php` template)
- [ ] Add Midtrans Server Key
- [ ] Add Midtrans Client Key
- [ ] Create `.env` file (optional, copy from `.env.example`)

### ✅ Verification
- [ ] Access homepage: http://localhost/tirtasanita ✓
- [ ] Access admin: http://localhost/tirtasanita/admin ✓
- [ ] Login dengan default credentials ✓
- [ ] View packages & facilities ✓

---

## 🔧 Configuration Quick Reference

### **Database** (`config/database.php`)
```php
$host = 'localhost';
$db_name = 'tirtasanita_db';
$username = 'root';
$password = '';  // Sesuaikan jika berbeda
```

### **Midtrans** (`config/midtrans.php`)
```php
$serverKey = 'YOUR_SERVER_KEY';   // Dari Midtrans Dashboard
$clientKey = 'YOUR_CLIENT_KEY';   // Dari Midtrans Dashboard
$isProduction = false;             // true untuk production
```

### **Environment** (`.env` - optional)
```
DB_HOST=localhost
DB_NAME=tirtasanita_db
DB_USER=root
DB_PASS=

MIDTRANS_SERVER_KEY=xxx
MIDTRANS_CLIENT_KEY=xxx
MIDTRANS_IS_PRODUCTION=false
```

---

## 🎯 First Steps After Installation

### 1️⃣ **Login as Admin**
- URL: http://localhost/tirtasanita/admin
- Username: 0812345678910
- Password: admin123

### 2️⃣ **Change Default Password**
- Click Profile / Settings
- Change admin password
- Update in database

### 3️⃣ **Verify Database**
- View admin/dashboard.php
- Check packages, facilities, payments loaded correctly

### 4️⃣ **Test Homepage**
- Access http://localhost/tirtasanita
- Verify packages displayed
- Check responsive design

### 5️⃣ **Configure Payment (If Using Midtrans)**
- Update `config/midtrans.php` dengan credentials
- Test payment workflow (optional)

---

## 📱 Admin Features Overview

### **Dashboard**
- Statistics: Total reservations, payments, users
- Recent activities
- Quick actions

### **Packages Management**
- CRUD operations
- Link facilities to packages
- Set weekday/weekend prices
- Manage categories

### **Reservations**
- View all reservations
- Update status
- View payment status
- Send tickets/notifications

### **Payments**
- View payment records
- Track payment status
- Multiple payment methods
- Transaction history

### **Users**
- Manage admin/cashier accounts
- Manage customer accounts
- View user history
- Activity logs

### **Settings**
- System configuration
- Business information
- Operating hours
- Notifications setup

---

## 💳 Cashier Panel Quick Start

### **Access Cashier Panel**
1. Login dengan cashier credentials (jika sudah dibuat admin)
2. Akan redirect ke cashier dashboard
3. Akses: http://localhost/tirtasanita/admin

### **Quick Actions**
- **Add Reservation:** Create new walk-in reservation
- **Print Ticket:** Print & generate ticket
- **Process Payment:** Record payment manually
- **View Sales:** Lihat report penjualan hari ini

---

## 🐛 Troubleshooting Quick Fixes

### ❌ "Database Connection Error"
**Fix:**
```bash
# Pastikan database sudah dibuat
# Dan file sudah diimport
# Verifikasi credentials di config/database.php
```

### ❌ "404 Not Found"
**Fix:**
```bash
# Cek URL benar: http://localhost/tirtasanita
# Pastikan web server running
# Clear browser cache (Ctrl+Shift+Del)
```

### ❌ "Call to undefined function"
**Fix:**
```php
# Cek require_once '../includes/functions.php' ada di file
# Verifikasi path relatif benar
```

### ❌ "CSRF Token Error"
**Fix:**
```bash
# Feature belum diimplementasikan
# Will be added in security update
```

### ❌ "Midtrans Webhook Error"
**Fix:**
```bash
# Buat file: config/midtrans.php
# Copy template dari existing file
# Update Server Key & Client Key
```

---

## 📚 Documentation Files

| File | Purpose |
|------|---------|
| `INSTALLATION.md` | Detailed installation for all methods |
| `TECHNICAL_REVIEW.md` | Security issues & recommendations |
| `PROJECT_STRUCTURE.md` | Project architecture & folders |
| `.env.example` | Environment variables template |
| `QUICK_START.md` | This file - quick setup |

---

## 🎨 Folder Structure Quick Reference

```
tirtasanita/
├── admin/              # Admin & cashier pages (40+ files)
├── config/             # Database & Midtrans config
├── includes/           # Helper functions & components
├── database/           # SQL schema file
├── webhook/            # Payment webhook handlers
├── css/, js/, img/     # Assets
├── lib/                # Libraries (Bootstrap, jQuery, etc.)
├── index.php           # Homepage
├── contact.php         # Contact page
└── INSTALLATION.md     # Complete installation guide
```

---

## ⚙️ Common Configuration Changes

### **Change Site Name**
**File:** `admin/dashboard.php`, `includes/navbar.php`
```php
// Find and replace
'Tirta Sanita Outbound' -> 'Your Site Name'
```

### **Change Admin WhatsApp**
**Database:**
```sql
UPDATE users SET whatsapp = '0858XXXXXXX' WHERE role = 'admin';
```

### **Change Database Credentials**
**File:** `config/database.php`
```php
private $host = 'new_host';
private $db_name = 'new_db';
private $username = 'new_user';
private $password = 'new_pass';
```

### **Setup Email Notifications**
**File:** `php.ini`
```ini
[mail function]
SMTP = smtp.gmail.com
smtp_port = 587
```

---

## 🔒 Security First Steps

### 🚨 CRITICAL - Do This First:

1. **Change Default Admin Password**
   ```bash
   # Login: 0812345678910 / admin123
   # Go to admin/profile.php
   # Change password immediately
   ```

2. **Configure Midtrans Credentials**
   ```bash
   # Edit: config/midtrans.php
   # Add your Server Key & Client Key
   ```

3. **Setup .env File** (recommended)
   ```bash
   # Copy .env.example to .env
   # Update with your actual values
   # Don't commit .env to git
   ```

---

## 📞 Need Help?

### **Documentation**
- See `INSTALLATION.md` untuk detailed setup
- See `TECHNICAL_REVIEW.md` untuk issue list
- See `PROJECT_STRUCTURE.md` untuk architecture

### **Common Issues**
1. Database connection? → Check `config/database.php`
2. Midtrans error? → Create `config/midtrans.php`
3. Admin access? → Check user credentials di database
4. File not found? → Verify folder structure

### **Contact**
- Repository: [GitHub Link]
- Issues: Open issue di GitHub
- Email: [Your Email]

---

## 🎯 Next Steps

### **Short Term (Day 1-3)**
- [ ] Complete installation
- [ ] Test all basic features
- [ ] Change default admin password
- [ ] Configure Midtrans account
- [ ] Test payment integration

### **Medium Term (Week 1-2)**
- [ ] Implement password hashing (SECURITY)
- [ ] Add CSRF token protection
- [ ] Setup email notifications
- [ ] Create additional admin/cashier accounts
- [ ] Customize site information

### **Long Term (Week 2+)**
- [ ] Add user registration
- [ ] Setup WhatsApp integration
- [ ] Create API documentation
- [ ] Setup automated backups
- [ ] Performance optimization

---

## ✅ Installation Verification Checklist

After setup, verify everything works:

- [ ] **Homepage Loads**: http://localhost/tirtasanita shows packages
- [ ] **Admin Accessible**: http://localhost/tirtasanita/admin login page
- [ ] **Login Works**: Can login with default credentials
- [ ] **Database OK**: Can view packages/facilities in admin
- [ ] **Images Load**: Logo & images visible
- [ ] **Responsive**: Works on mobile/tablet view
- [ ] **Forms Work**: Contact form, login form functional
- [ ] **No Errors**: Console/network tab shows no errors

---

## 🎉 You're Ready!

Sistem Tirta Sanita Outbound sudah siap digunakan. Selamat mengembangkan! 🚀

**Last Updated:** June 26, 2026  
**Version:** 1.0

