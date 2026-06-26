# 📋 Panduan Instalasi & Menjalankan Tirta Sanita Outbound

Dokumentasi lengkap untuk menginstal dan menjalankan project **Tirta Sanita Outbound** dengan berbagai metode.

---

## 📑 Daftar Isi
1. [Ringkasan Project](#ringkasan-project)
2. [Prasyarat Sistem](#prasyarat-sistem)
3. [Instalasi dengan Laragon](#instalasi-dengan-laragon)
4. [Instalasi dengan XAMPP](#instalasi-dengan-xampp)
5. [Setup Manual](#setup-manual)
6. [Clone dari GitHub](#clone-dari-github)
7. [Konfigurasi Lanjutan](#konfigurasi-lanjutan)
8. [Menjalankan Project](#menjalankan-project)
9. [Akses Default](#akses-default)
10. [Troubleshooting](#troubleshooting)
11. [Catatan Penting](#catatan-penting)

---

## 📌 Ringkasan Project

**Tirta Sanita Outbound** adalah sistem manajemen reservasi dan penjualan tiket untuk tempat wisata outbound. Sistem ini mencakup:

- 🌐 Website publik dengan katalog paket wisata
- 📊 Dashboard admin untuk manajemen data
- 💳 Panel kasir untuk penjualan tiket instant
- 💰 Integrasi payment gateway (Midtrans)
- 📱 Notifikasi via WhatsApp
- 📧 Konfirmasi pembayaran via email

### **Tech Stack:**
- **Backend:** PHP 7.4+ (Procedural)
- **Database:** MySQL 5.7+ / MariaDB
- **Frontend:** Bootstrap 5, jQuery
- **Payment Gateway:** Midtrans
- **Server:** Apache

---

## 🔧 Prasyarat Sistem

Sebelum memulai, pastikan sistem Anda memiliki:

### **Software yang Diperlukan:**
- **PHP** 7.4 atau lebih tinggi
- **MySQL** 5.7 atau lebih tinggi (atau MariaDB)
- **Composer** (untuk package management)
- **Git** (opsional, untuk clone dari GitHub)

### **Ekstensi PHP yang Diperlukan:**
- PDO
- PDO_MySQL
- cURL (untuk Midtrans API)
- OpenSSL

### **Browser yang Disupport:**
- Google Chrome (versi terbaru)
- Mozilla Firefox (versi terbaru)
- Microsoft Edge (versi terbaru)
- Safari (versi terbaru)

### **Ruang Disk:**
- Minimal 500 MB untuk instalasi lengkap

---

## 🚀 Instalasi dengan Laragon

**Laragon** adalah local development environment terbaik untuk Windows yang sudah include semua tools yang diperlukan.

### **Langkah 1: Download & Install Laragon**

1. **Kunjungi website Laragon:** https://laragon.org/
2. **Download versi Full** (sudah include Apache, MySQL, PHP, Composer)
   - File: `laragon-wamp-5.x.x-full.exe` (~250MB)
3. **Jalankan installer:**
   - Double-click file installer
   - Klik "Next" untuk melanjutkan
   - Accept license agreement
4. **Pilih lokasi instalasi:**
   - Default: `C:\laragon` (RECOMMENDED)
   - Klik "Next"
5. **Pilih komponen:**
   - Pastikan semua komponen tercentang
6. **Tunggu instalasi selesai** (~5-10 menit)
7. **Finish dan buka Laragon**

### **Langkah 2: Clone atau Copy Project ke Folder www**

#### **Opsi A: Clone dari GitHub** (Direkomendasikan)
1. **Buka Laragon Terminal:**
   - Di aplikasi Laragon, klik tombol "Terminal"
2. **Navigasi ke folder www:**
   ```bash
   cd C:\laragon\www
   ```
3. **Clone repository:**
   ```bash
   git clone https://github.com/username/tirtasanita.git
   cd tirtasanita
   ```
4. **Tunggu hingga clone selesai**

#### **Opsi B: Download & Extract ZIP**
1. **Download project sebagai ZIP** dari GitHub
2. **Extract ke folder `C:\laragon\www\tirtasanita`**
3. **Buka terminal dan navigasi:**
   ```bash
   cd C:\laragon\www\tirtasanita
   ```

### **Langkah 3: Install Dependencies dengan Composer**

1. **Di dalam folder tirtasanita, jalankan:**
   ```bash
   composer install
   ```
   Output yang diharapkan:
   ```
   Installing dependencies from lock file
   - Installing midtrans/midtrans-php (v2.x.x)
   ```

2. **Tunggu hingga dependencies selesai** (~2-3 menit)

### **Langkah 4: Setup Database**

#### **Metode A: Menggunakan phpMyAdmin GUI**

1. **Buka phpMyAdmin:**
   - Di aplikasi Laragon, klik tombol "Database"
   - Atau buka browser: http://localhost/phpmyadmin

2. **Login dengan:**
   - Username: `root`
   - Password: (kosong)

3. **Buat database baru:**
   - Di sidebar kiri, klik "New"
   - Nama database: `tirtasanita_db`
   - Collation: `utf8mb4_general_ci`
   - Klik "Create"

4. **Import file SQL:**
   - Pilih database `tirtasanita_db` (di sidebar kiri)
   - Klik tab "Import"
   - Klik "Choose File"
   - Pilih: `database/tirtasanita_db.sql`
   - Klik "Go"
   - Tunggu hingga import selesai

#### **Metode B: Menggunakan Command Line**

```bash
mysql -u root < database/tirtasanita_db.sql
```

### **Langkah 5: Verifikasi Konfigurasi**

File `config/database.php` sudah ter-konfigurasi dengan benar:
```php
private $host = 'localhost';
private $db_name = 'tirtasanita_db';
private $username = 'root';
private $password = '';
```

**Jika menggunakan setup yang berbeda**, edit file `config/database.php` sesuai konfigurasi Anda.
private $password = '';
```

Jika berbeda dengan setup Anda, edit file ini sesuai kebutuhan.

### **Langkah 6: Jalankan Project**

1. **Start Laragon:**
   - Buka aplikasi Laragon
   - Klik tombol **Start All** (warna hijau)

2. **Akses di Browser:**
   - Homepage: `http://localhost/tirtasanita`
   - Admin Panel: `http://localhost/tirtasanita/admin`

---

## 🛠️ Instalasi dengan XAMPP

**XAMPP** adalah paket Apache, MySQL, PHP, dan Perl untuk Windows.

### **Langkah 1: Download & Install XAMPP**

1. Kunjungi website [Apache Friends](https://www.apachefriends.org/)
2. Download versi **XAMPP** (pilih versi PHP 7.4 atau lebih tinggi)
3. Jalankan installer
4. Pilih komponen yang ingin diinstal (pastikan MySQL dan PHP dipilih)
5. Pilih lokasi instalasi (default: `C:\xampp`)
6. Tunggu hingga instalasi selesai

### **Langkah 2: Download & Install Composer**

1. Kunjungi website [Composer](https://getcomposer.org/)
2. Download **Composer Setup** untuk Windows
3. Jalankan installer dan pilih PHP executable dari XAMPP
4. Selesaikan instalasi

### **Langkah 3: Clone atau Copy Project ke Folder htdocs**

#### **Opsi A: Clone dari GitHub**
```bash
cd C:\xampp\htdocs
git clone https://github.com/username/tirtasanita.git
cd tirtasanita
```

#### **Opsi B: Copy Manual**
1. Download project (ZIP)
2. Extract ke folder `C:\xampp\htdocs\tirtasanita`

### **Langkah 4: Install Dependencies dengan Composer**

1. Buka **Command Prompt** atau **PowerShell**
2. Navigasi ke folder project:
   ```bash
   cd C:\xampp\htdocs\tirtasanita
   ```
3. Jalankan composer:
   ```bash
   composer install
   ```

### **Langkah 5: Start XAMPP & Setup Database**

1. **Jalankan XAMPP Control Panel:**
   - Start **Apache**
   - Start **MySQL**

2. **Buka phpMyAdmin:**
   - Akses `http://localhost/phpmyadmin`

3. **Buat Database:**
   - Username: `root`
   - Password: (kosong)
   - Buat database: `tirtasanita_db`

4. **Import Database:**
   - Pilih database `tirtasanita_db`
   - Tab **Import**
   - Pilih: `database/tirtasanita_db.sql`
   - Klik **GO**

### **Langkah 6: Verifikasi Konfigurasi**

Edit file `config/database.php` jika diperlukan:
```php
private $host = 'localhost';
private $db_name = 'tirtasanita_db';
private $username = 'root';
private $password = '';
```

### **Langkah 7: Jalankan Project**

Akses di browser:
- Homepage: `http://localhost/tirtasanita`
- Admin Panel: `http://localhost/tirtasanita/admin`

---

## 🔧 Setup Manual

Untuk setup manual tanpa Laragon atau XAMPP.

### **Prasyarat:**
- PHP 7.4+ terinstal dengan benar
- MySQL Server berjalan
- Composer terinstal

### **Langkah 1: Persiapkan Folder Project**

1. Tentukan lokasi project (contoh: `C:\Projects\tirtasanita`)
2. Extract atau copy semua file project ke folder tersebut

### **Langkah 2: Install Dependencies**

```bash
cd C:\Projects\tirtasanita
composer install
```

### **Langkah 3: Setup Web Server (IIS atau Apache)**

#### **Jika menggunakan Apache:**

1. Edit file `httpd.conf` atau `httpd-vhosts.conf`
2. Tambahkan Virtual Host:
   ```apache
   <VirtualHost *:80>
       ServerName tirtasanita.local
       DocumentRoot "C:\Projects\tirtasanita"
       
       <Directory "C:\Projects\tirtasanita">
           Options Indexes FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

3. Edit file `hosts` (Windows):
   - Buka: `C:\Windows\System32\drivers\etc\hosts`
   - Tambahkan baris: `127.0.0.1   tirtasanita.local`

#### **Jika menggunakan PHP Built-in Server:**

Buka Command Prompt dan jalankan:
```bash
cd C:\Projects\tirtasanita
php -S localhost:8000
```

Akses: `http://localhost:8000`

### **Langkah 4: Setup Database**

1. **Buka MySQL Command Line atau MySQL Workbench**

2. **Buat Database:**
   ```sql
   CREATE DATABASE tirtasanita_db;
   USE tirtasanita_db;
   ```

3. **Import File SQL:**
   ```bash
   mysql -u root -p tirtasanita_db < database/tirtasanita_db.sql
   ```

### **Langkah 5: Konfigurasi Database**

Edit file `config/database.php` sesuaikan dengan setting Anda:
```php
private $host = 'localhost';      // Host MySQL
private $db_name = 'tirtasanita_db'; // Nama database
private $username = 'root';       // Username MySQL
private $password = '';           // Password MySQL (kosong jika tidak ada)
```

### **Langkah 6: Jalankan Project**

Akses di browser sesuai konfigurasi web server Anda:
- Jika Apache Virtual Host: `http://tirtasanita.local`
- Jika PHP Built-in: `http://localhost:8000`

---

## 📥 Clone dari GitHub

Metode tercepat untuk developer.

### **Prasyarat:**
- Git sudah terinstal
- Akses ke repository (Public atau Private)

### **Langkah 1: Clone Repository**

Pilih lokasi untuk project, lalu buka Command Prompt/Terminal:

```bash
# Clone project
git clone https://github.com/username/tirtasanita.git

# Masuk ke folder project
cd tirtasanita
```

### **Langkah 2: Install Dependencies**

```bash
composer install
```

### **Langkah 3: Setup Environment**

Jika ada file `.env.example`, copy menjadi `.env` dan sesuaikan:
```bash
cp .env.example .env
```

Edit `.env` dengan setting database Anda.

### **Langkah 4: Setup Database**

```bash
# Import database
mysql -u root -p tirtasanita_db < database/tirtasanita_db.sql
```

### **Langkah 5: Jalankan Project**

Sesuaikan dengan development environment Anda (Laragon, XAMPP, atau Manual).

---

## 💾 Konfigurasi Database

### **Informasi Koneksi Default:**

| Parameter | Value |
|-----------|-------|
| Host | localhost |
| Database | tirtasanita_db |
| Username | root |
| Password | (kosong) |
| Port | 3306 (default MySQL) |

### **Jika Anda Mengubah Credentials:**

Edit file `config/database.php`:
```php
<?php
class Database {
    private $host = 'your_host';           // Ganti host
    private $db_name = 'your_database';    // Ganti nama database
    private $username = 'your_username';   // Ganti username
    private $password = 'your_password';   // Ganti password
    private $conn;
    // ...
}
?>
```

### **Testing Koneksi Database:**

Buat file `test_db.php` di root folder:
```php
<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "✓ Koneksi Database Berhasil!";
    echo "<br>Database: tirtasanita_db";
} else {
    echo "✗ Koneksi Database Gagal!";
}
?>
```

Akses: `http://localhost/tirtasanita/test_db.php`

---

## ▶️ Menjalankan Project

### **1. Homepage (Public)**

**URL:** `http://localhost/tirtasanita` (sesuaikan dengan setup Anda)

**Fitur Tersedia:**
- Informasi paket wisata
- Pemesanan/reservasi
- Gallery
- Contact information
- Pembayaran online (Midtrans)

### **2. Admin Panel**

**URL:** `http://localhost/tirtasanita/admin`

**Login Credentials:**
- Buat user admin melalui database atau panel admin

**Fitur Admin:**
- Dashboard
- Manajemen paket
- Manajemen fasilitas
- Manajemen reservasi
- Manajemen pembayaran
- Manajemen user
- Settings

### **3. Cashier Panel**

**URL:** `http://localhost/tirtasanita/admin` (login sebagai cashier)

**Fitur Cashier:**
- Dashboard cashier
- Pemesanan instant ticket
- Manajemen reservasi
- Pemrosesan pembayaran
- Laporan penjualan
- Print ticket

---

## 🎯 Struktur Folder Project

```
tirtasanita/
├── admin/                  # Admin & Cashier Panel
├── config/                 # Konfigurasi (database, midtrans)
├── database/               # File SQL database
├── includes/               # Helper functions, navbar, footer
├── css/                    # Stylesheet
├── js/                     # JavaScript files
├── img/                    # Images & icons
├── lib/                    # External libraries
├── webhook/                # Webhook handlers (Midtrans)
├── uploads/                # User uploads
├── vendor/                 # Composer packages
├── composer.json           # Dependencies
├── index.php               # Homepage
├── contact.php             # Contact page
├── 404.html                # 404 page
└── INSTALLATION.md         # This file
```

---

## 🐛 Troubleshooting

### **Masalah: Database Connection Error**

**Solusi:**
1. Pastikan MySQL server sudah berjalan
2. Verifikasi username & password di `config/database.php`
3. Pastikan database `tirtasanita_db` sudah dibuat
4. Test koneksi dengan file `test_db.php`

### **Masalah: Composer Not Found**

**Solusi:**
```bash
# Windows - Install Composer secara global
# atau gunakan:
php composer.phar install

# Pastikan Composer sudah di PATH
composer --version
```

### **Masalah: 404 Page Not Found**

**Solusi:**
1. Verifikasi folder project berada di lokasi yang benar
   - Laragon: `C:\laragon\www\tirtasanita`
   - XAMPP: `C:\xampp\htdocs\tirtasanita`
2. Pastikan Apache/Web Server sudah berjalan
3. Clear browser cache
4. Akses ulang dengan URL yang benar

### **Masalah: Permission Denied**

**Solusi:**
1. Pastikan folder project memiliki permission read/write
   ```bash
   # Windows - Run as Administrator
   icacls "C:\path\to\tirtasanita" /grant:r "%USERNAME%":(F)
   ```

### **Masalah: White Screen of Death (WSoD)**

**Solusi:**
1. Enable error reporting di PHP
2. Check file `php.ini`
3. Set `display_errors = On`
4. Check error log di server

### **Masalah: Session Not Working**

**Solusi:**
1. Pastikan folder `sessions` memiliki write permission
2. Periksa konfigurasi session di `php.ini`
3. Restart web server

### **Masalah: Midtrans Webhook Not Triggered**

**Solusi:**
1. Verify webhook URL di Midtrans Dashboard
2. Pastikan URL public dan accessible
3. Check error log di `webhook/midtrans.php`
4. Test dengan Curl:
   ```bash
   curl -X POST http://yourdomain.com/tirtasanita/webhook/midtrans.php
   ```

---

## 📚 Tips Pengembangan

### **1. Debugging dengan var_dump()**
```php
<?php
$data = [...];
var_dump($data);
exit;
?>
```

### **2. Logging Errors**
Edit `php.ini`:
```ini
error_log = "C:\laragon\logs\php-error.log"
log_errors = On
```

### **3. Database Query Debugging**
Aktifkan error mode di `config/database.php`:
```php
$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
```

### **4. Version Control Best Practices**
```bash
# Jangan commit vendor folder
# Jangan commit sensitive files (.env, config dengan password)
# Commit hanya file yang diperlukan
```

---

## ✅ Checklist Post-Installation

- [ ] Database berhasil terbuat
- [ ] Composer dependencies terinstal
- [ ] Web server berjalan
- [ ] Homepage accessible
- [ ] Admin panel accessible
- [ ] Database connection OK
- [ ] Session working
- [ ] Upload folder writable
- [ ] Midtrans configured (jika menggunakan payment)
- [ ] Email configuration (jika menggunakan email)

---

## 📞 Support & Bantuan

Jika mengalami masalah:
1. Cek dokumentasi ini lagi
2. Lihat error message di browser atau server logs
3. Hubungi tim development
4. Buat issue di GitHub repository

---

## 📝 Catatan Versi

**Version:** 1.0  
**Last Updated:** June 2026  
**Project:** Tirta Sanita Outbound  

---

Selamat! Project sudah siap digunakan. Selamat mengembangkan! 🎉
