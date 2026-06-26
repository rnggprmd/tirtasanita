# 🔐 Login Troubleshooting Guide

**Problem:** Tidak bisa login ke admin panel  
**Solution:** Follow guide ini step-by-step

---

## 🔍 Diagnosis

Jika Anda mendapat error "Akun tidak ditemukan atau tidak memiliki akses admin", kemungkinan:

1. ❌ Database belum di-import
2. ❌ Database sudah ada tapi dengan data lama
3. ❌ WhatsApp number salah
4. ❌ Password salah

---

## ✅ SOLUSI CEPAT - Gunakan Verification Script

Saya sudah membuat script untuk verifikasi dan fix otomatis:

### **Langkah 1: Jalankan Verification Script**

Buka di browser:
```
http://localhost/tirtasanita/verify_admin.php
```

Script ini akan:
1. ✓ Check database connection
2. ✓ Get current admin data
3. ✓ Compare dengan expected credentials
4. ✓ Automatically fix jika ada mismatch
5. ✓ Show Anda credentials yang benar

### **Langkah 2: Cek Hasil**

Script akan menampilkan salah satu:

**Jika ✅ Credentials Correct:**
```
✅ ✅ ✅ CREDENTIALS ARE CORRECT!
You can login with:
WhatsApp: 0812345678910
Password: admin123
```

**Jika ❌ Credentials Mismatch:**
```
⚠️ CREDENTIALS MISMATCH!
🔧 Attempting to fix credentials...
✅ UPDATE SUCCESSFUL!

Your new credentials are:
  WhatsApp: 0812345678910
  Password: admin123
```

### **Langkah 3: Login Admin**

Setelah script selesai, buka:
```
http://localhost/tirtasanita/admin
```

Gunakan credentials dari script output.

---

## 🛠️ MANUAL SOLUTION - Jika Script Gagal

### **Option A: Update via phpMyAdmin**

1. **Buka phpMyAdmin:**
   ```
   http://localhost/phpmyadmin
   ```

2. **Login dengan:**
   - Username: `root`
   - Password: (kosong)

3. **Select database:** `tirtasanita_db`

4. **Open table:** `users`

5. **Edit row admin (ID = 1):**
   - `whatsapp`: `0812345678910`
   - `password`: `admin123`

6. **Save changes**

### **Option B: Update via SQL Query**

Di phpMyAdmin, buka tab **SQL** dan jalankan:

```sql
UPDATE users 
SET whatsapp = '0812345678910', password = 'admin123' 
WHERE id = 1 AND role = 'admin';
```

Klik **Go**

### **Option C: Update via MySQL Command Line**

```bash
mysql -u root tirtasanita_db -e "UPDATE users SET whatsapp = '0812345678910', password = 'admin123' WHERE id = 1;"
```

---

## 🗑️ NUCLEAR OPTION - Fresh Database Import

Jika semua di atas tidak berhasil, import database dari awal:

### **Step 1: Delete Current Database**

Di phpMyAdmin:
1. Pilih database `tirtasanita_db`
2. Klik "Operations"
3. Klik "Drop Database"
4. Confirm

**Atau via command:**
```bash
mysql -u root -e "DROP DATABASE tirtasanita_db;"
```

### **Step 2: Create New Database**

**Via phpMyAdmin:**
1. Klik "New"
2. Database name: `tirtasanita_db`
3. Collation: `utf8mb4_general_ci`
4. Klik "Create"

**Atau via command:**
```bash
mysql -u root -e "CREATE DATABASE tirtasanita_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
```

### **Step 3: Import SQL File**

**Via phpMyAdmin:**
1. Pilih database `tirtasanita_db`
2. Tab **Import**
3. Pilih file: `database/tirtasanita_db.sql`
4. Klik **Go**

**Atau via command:**
```bash
mysql -u root tirtasanita_db < database/tirtasanita_db.sql
```

### **Step 4: Verify**

Jalankan verification script lagi:
```
http://localhost/tirtasanita/verify_admin.php
```

---

## 📋 Expected Credentials

Setelah semua proses di atas, Anda seharusnya bisa login dengan:

```
URL:      http://localhost/tirtasanita/admin
WhatsApp: 0812345678910
Password: admin123
```

---

## 🔍 Debug Checklist

Jika masih tidak bisa login, verifikasi:

- [ ] **Database exists?**
  ```sql
  SHOW DATABASES;
  ```
  Harus ada `tirtasanita_db`

- [ ] **Users table exists?**
  ```sql
  USE tirtasanita_db;
  SHOW TABLES;
  ```
  Harus ada table `users`

- [ ] **Admin user exists?**
  ```sql
  SELECT * FROM users WHERE role = 'admin';
  ```
  Harus ada 1 row

- [ ] **Credentials correct?**
  ```sql
  SELECT whatsapp, password FROM users WHERE role = 'admin';
  ```
  Harus return:
  ```
  0812345678910 | admin123
  ```

- [ ] **PHP can connect to database?**
  Buka: http://localhost/tirtasanita/verify_admin.php
  Harus tidak ada error "Database connection failed"

- [ ] **Admin page accessible?**
  ```
  http://localhost/tirtasanita/admin
  ```
  Harus buka form login

---

## 🆘 If Still Having Issues

### **Check Browser Console for Errors:**
1. Press F12 to open Developer Tools
2. Check **Console** tab for JavaScript errors
3. Check **Network** tab for failed requests

### **Check Server Logs:**

**Laragon:**
- Apache logs: `C:\laragon\logs\apache_error.log`
- PHP logs: `C:\laragon\logs\php_error.log`

**XAMPP:**
- Apache logs: `C:\xampp\apache\logs\error.log`
- PHP logs: Check `php.ini` error_log location

### **Common Errors:**

**Error: "SQLSTATE[HY000]: General error"**
- Database credentials wrong atau database tidak ada
- Solution: Check database config di `config/database.php`

**Error: "Access denied for user"**
- MySQL username/password salah
- Solution: Verify `config/database.php` credentials

**Error: "Table 'tirtasanita_db.users' doesn't exist"**
- Database belum di-import
- Solution: Import `database/tirtasanita_db.sql`

---

## ✅ Final Checklist

Sebelum keluar troubleshooting, pastikan:

- [x] Verification script run successfully
- [x] Credentials updated
- [x] Can access http://localhost/tirtasanita/admin
- [x] Login form loads without errors
- [x] Can login dengan credentials:
  - WhatsApp: `0812345678910`
  - Password: `admin123`
- [x] Redirect to dashboard successful
- [x] Can see admin panel

---

## 📞 Need Help?

Jika masih stuck, provide info ini:

1. **What error message Anda lihat?**
   - Screenshot atau exact error text

2. **Database status:**
   - Run verification script: `http://localhost/tirtasanita/verify_admin.php`
   - Copy semua output

3. **MySQL connection:**
   - Test bisa connect ke MySQL?
   - MySQL running?

4. **Setup method:**
   - Anda pakai Laragon atau XAMPP?
   - Database sudah di-import?

---

## 🚀 Once Logged In

Setelah berhasil login:

1. **Change password** (RECOMMENDED)
   - Go to Admin > Profile
   - Change password dari `admin123` ke password yang lebih kuat

2. **Verify sistem** berjalan:
   - Dashboard menampilkan data?
   - Packages terlihat?
   - Facilities terlihat?

3. **Test payment** (optional)
   - Go to website homepage
   - Try create reservation
   - Test Midtrans payment

---

**Last Updated:** June 26, 2026  
**Status:** Updated with verification script

