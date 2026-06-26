# ⚡ Quick Start Guide - Tirta Sanita Outbound

Panduan cepat untuk mulai dalam 5 menit!

---

## 🚀 Quick Start (5 Menit)

### **Jika Menggunakan Laragon:**

```bash
# 1. Clone ke folder www
cd C:\laragon\www
git clone https://github.com/username/tirtasanita.git

# 2. Install dependencies
cd tirtasanita
composer install

# 3. Setup database (via phpMyAdmin atau Laragon DB Manager)
# - Buat database: tirtasanita_db
# - Import: database/tirtasanita_db.sql

# 4. Start Laragon - klik "Start All"

# 5. Akses browser
# Homepage: http://localhost/tirtasanita
# Admin: http://localhost/tirtasanita/admin
```

### **Jika Menggunakan XAMPP:**

```bash
# 1. Clone ke htdocs
cd C:\xampp\htdocs
git clone https://github.com/username/tirtasanita.git

# 2. Install dependencies
cd tirtasanita
composer install

# 3. Start Apache & MySQL di XAMPP Control Panel

# 4. Setup database di phpMyAdmin
# - Buat database: tirtasanita_db
# - Import: database/tirtasanita_db.sql

# 5. Akses browser
# Homepage: http://localhost/tirtasanita
# Admin: http://localhost/tirtasanita/admin
```

### **Jika Menggunakan PHP Built-in Server:**

```bash
# 1. Clone project
git clone https://github.com/username/tirtasanita.git
cd tirtasanita

# 2. Install dependencies
composer install

# 3. Setup database via MySQL CLI
mysql -u root -p < database/tirtasanita_db.sql

# 4. Jalankan server
php -S localhost:8000

# 5. Akses browser
# Homepage: http://localhost:8000
# Admin: http://localhost:8000/admin
```

---

## 🔑 Login Credentials

### **Admin Akun:**
- **Nomor WhatsApp:** (ada di database)
- **Password:** (ada di database)

**Atau buat user baru via database dengan query:**
```sql
INSERT INTO users (name, whatsapp, password, role) 
VALUES ('Admin User', '628123456789', 'password123', 'admin');
```

### **Cashier Akun:**
```sql
INSERT INTO users (name, whatsapp, password, role) 
VALUES ('Cashier User', '628123456790', 'password123', 'cashier');
```

---

## 📂 File Penting

| File | Fungsi |
|------|--------|
| `config/database.php` | Konfigurasi database |
| `config/midtrans.php` | Konfigurasi payment gateway |
| `database/tirtasanita_db.sql` | Database schema |
| `includes/functions.php` | Helper functions |
| `includes/navbar.php` | Navigation bar |
| `admin/dashboard.php` | Admin dashboard |

---

## 🌐 URL Penting

| URL | Fungsi |
|-----|--------|
| `/` | Homepage |
| `/contact.php` | Contact page |
| `/admin` | Admin login |
| `/admin/dashboard.php` | Admin dashboard |
| `/admin/cashier-dashboard.php` | Cashier dashboard |

---

## 🐛 Jika Ada Error

### **Error: "Connection error: SQLSTATE"**
- [ ] MySQL running?
- [ ] Database `tirtasanita_db` sudah dibuat?
- [ ] Check `config/database.php`

### **Error: "Class not found"**
- Jalankan: `composer install`

### **Error: "404 Page not found"**
- Check folder path (Laragon: `C:\laragon\www\tirtasanita`)
- Web server sudah running?

### **Error: "Permission denied"**
- Check folder permissions
- Right-click folder → Properties → Security

---

## 📚 Dokumentasi Lengkap

Baca file `INSTALLATION.md` untuk panduan instalasi detail.

---

## ✨ Next Steps

1. ✅ Install project
2. ✅ Setup database
3. ✅ Login ke admin panel
4. 📝 Explore semua fitur
5. 🔧 Customize sesuai kebutuhan
6. 🚀 Deploy ke production

---

Happy coding! 🎉

---

**Need Help?**
- Check `INSTALLATION.md`
- See troubleshooting section
- Contact development team
