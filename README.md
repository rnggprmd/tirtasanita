# 🏞️ Tirta Sanita Outbound

**Platform Manajemen Reservasi & Pemesanan Online untuk Wisata Outbound**

![Version](https://img.shields.io/badge/version-1.0-blue)
![License](https://img.shields.io/badge/license-MIT-green)
![PHP](https://img.shields.io/badge/PHP-7.4+-purple)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-blue)

---

## 📖 Daftar Isi

- [Tentang Project](#tentang-project)
- [Fitur Utama](#fitur-utama)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Quick Start](#quick-start)
- [Instalasi Lengkap](#instalasi-lengkap)
- [Dokumentasi](#dokumentasi)
- [Struktur Database](#struktur-database)
- [API Integration](#api-integration)
- [Contributing](#contributing)
- [License](#license)
- [Support](#support)

---

## 🎯 Tentang Project

**Tirta Sanita Outbound** adalah platform web yang dirancang untuk memudahkan manajemen reservasi dan pemesanan tiket wisata outbound. Platform ini menyediakan antarmuka yang user-friendly untuk pelanggan serta dashboard admin yang komprehensif untuk manajemen bisnis.

### **Target User:**
- 👥 Wisatawan yang ingin booking paket outbound
- 👨‍💼 Admin untuk mengelola paket dan reservasi
- 💳 Cashier untuk pemrosesan pembayaran dan pemesanan instant ticket

---

## ✨ Fitur Utama

### **👨‍💼 Untuk Admin:**
- ✅ Dashboard dengan analytics real-time
- 📦 Manajemen paket wisata (CRUD)
- 🏢 Manajemen fasilitas
- 👥 Manajemen user (admin & cashier)
- 📋 Manajemen reservasi
- 💳 Manajemen pembayaran
- ⚙️ Settings & konfigurasi sistem
- 📊 Laporan penjualan
- 🎫 Print & send ticket

### **💳 Untuk Cashier:**
- 📊 Dashboard cashier
- 🎫 Pemesanan instant ticket
- 📋 Manajemen reservasi
- 💰 Pemrosesan pembayaran
- 📄 Laporan penjualan
- 🖨️ Print ticket
- 📤 Send ticket via WhatsApp/Email

### **👥 Untuk Customer:**
- 🏠 Homepage dengan informasi paket
- 📝 Form pemesanan/reservasi
- 💳 Pembayaran online (Midtrans)
- 📧 Konfirmasi via email
- 📱 Support WhatsApp
- 🎨 Gallery
- 📞 Contact page

---

## 🛠️ Teknologi yang Digunakan

### **Backend:**
- ![PHP](https://img.shields.io/badge/-PHP-777BB4?style=flat-square&logo=php&logoColor=white) **PHP 7.4+** - Server-side language
- ![MySQL](https://img.shields.io/badge/-MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white) **MySQL 5.7+** - Database
- ![Composer](https://img.shields.io/badge/-Composer-885630?style=flat-square&logo=composer&logoColor=white) **Composer** - Package manager

### **Frontend:**
- ![HTML5](https://img.shields.io/badge/-HTML5-E34C26?style=flat-square&logo=html5&logoColor=white) **HTML5** - Markup
- ![CSS3](https://img.shields.io/badge/-CSS3-1572B6?style=flat-square&logo=css3&logoColor=white) **CSS3** - Styling
- ![JavaScript](https://img.shields.io/badge/-JavaScript-F7DF1E?style=flat-square&logo=javascript&logoColor=black) **JavaScript** - Interactivity
- ![Bootstrap](https://img.shields.io/badge/-Bootstrap-7952B3?style=flat-square&logo=bootstrap&logoColor=white) **Bootstrap 5** - UI Framework

### **Dependencies:**
- **Midtrans PHP SDK** - Payment gateway integration
- **PDO** - Database abstraction layer

### **Integrated Services:**
- 🏦 **Midtrans** - Payment processing
- 📧 **SMTP** - Email service
- 📱 **WhatsApp Business API** - Customer communication

---

## 🚀 Quick Start

### **Prerequisite:**
- PHP 7.4+
- MySQL 5.7+
- Composer
- Git

### **Installation (3 Steps):**

```bash
# 1. Clone repository
git clone https://github.com/username/tirtasanita.git
cd tirtasanita

# 2. Install dependencies
composer install

# 3. Import database
mysql -u root -p tirtasanita_db < database/tirtasanita_db.sql
```

### **Run with Laragon (Recommended):**
```bash
# Copy to C:\laragon\www\tirtasanita
# Open Laragon → Click Start All
# Access: http://localhost/tirtasanita
```

### **Run with XAMPP:**
```bash
# Copy to C:\xampp\htdocs\tirtasanita
# Start Apache & MySQL in XAMPP Control Panel
# Access: http://localhost/tirtasanita
```

### **Run with PHP Built-in Server:**
```bash
# Terminal/CMD
php -S localhost:8000
# Access: http://localhost:8000
```

---

## 📚 Instalasi Lengkap

Untuk instruksi instalasi yang detail, lihat:

👉 **[INSTALLATION.md](INSTALLATION.md)** - Panduan instalasi lengkap dengan berbagai metode

👉 **[QUICKSTART.md](QUICKSTART.md)** - Panduan cepat untuk mulai dalam 5 menit

---

## 📑 Dokumentasi

### **Dokumentasi Teknis:**
- [INSTALLATION.md](INSTALLATION.md) - Setup & instalasi
- [QUICKSTART.md](QUICKSTART.md) - Quick start guide
- [admin/CASHIER_WORKFLOW.md](admin/CASHIER_WORKFLOW.md) - Workflow cashier
- [config/database.php](config/database.php) - Database configuration

### **Database Schema:**
Database sudah tersedia di: `database/tirtasanita_db.sql`

### **API Documentation:**
- Payment webhook: `webhook/midtrans.php`
- Database layer: `config/database.php`

---

## 📊 Struktur Database

### **Main Tables:**

```
users
├── id (PK)
├── name
├── whatsapp
├── email
├── password
├── role (admin, cashier, customer)

packages
├── id (PK)
├── name
├── description
├── price_weekday
├── price_weekend
├── capacity
├── category_id (FK)

reservations
├── id (PK)
├── package_id (FK)
├── user_id (FK)
├── visit_date
├── num_visitors
├── total_price
├── status

payments
├── id (PK)
├── reservation_id (FK)
├── transaction_id
├── amount
├── status
├── payment_method
├── created_at

facilities
├── id (PK)
├── name
├── description
├── icon
```

---

## 💳 API Integration

### **Midtrans Payment Gateway**

**Configuration File:** `config/midtrans.php`

**Features:**
- Credit card payment
- Bank transfer
- E-wallet
- Real-time transaction status update
- Webhook notification

**Webhook URL:** `https://yourdomain.com/tirtasanita/webhook/midtrans.php`

**Setup Midtrans:**
1. Create account at [Midtrans Dashboard](https://dashboard.midtrans.com)
2. Get Server Key & Client Key
3. Configure in `config/midtrans.php`
4. Setup webhook URL
5. Test transaction

---

## 📁 Folder Structure

```
tirtasanita/
├── admin/                    # Admin & Cashier Panel
│   ├── dashboard.php         # Admin dashboard
│   ├── cashier-dashboard.php # Cashier dashboard
│   ├── packages.php          # Package management
│   ├── reservations.php      # Reservation management
│   └── ...
├── config/                   # Configuration files
│   ├── database.php          # Database connection
│   └── midtrans.php          # Payment configuration
├── database/                 # Database files
│   └── tirtasanita_db.sql    # Database schema & data
├── includes/                 # Reusable components
│   ├── navbar.php            # Navigation bar
│   ├── footer.php            # Footer
│   ├── topbar.php            # Top bar
│   └── functions.php         # Helper functions
├── css/                      # Stylesheets
│   ├── bootstrap.min.css
│   └── style.css
├── js/                       # JavaScript files
│   └── main.js
├── img/                      # Images & icons
│   └── icon/                 # Icon assets
├── lib/                      # External libraries
│   ├── animate/              # Animate.css
│   ├── owlcarousel/          # Owl Carousel
│   └── lightbox/             # Lightbox gallery
├── uploads/                  # User uploads directory
├── vendor/                   # Composer packages
├── webhook/                  # Webhook handlers
│   └── midtrans.php          # Midtrans webhook
├── .gitignore                # Git ignore rules
├── composer.json             # PHP dependencies
├── composer.lock             # Dependency lock file
├── index.php                 # Homepage
├── contact.php               # Contact page
├── 404.html                  # 404 error page
├── README.md                 # This file
├── INSTALLATION.md           # Installation guide
├── QUICKSTART.md             # Quick start guide
└── CHANGELOG.md              # Version history
```

---

## 🔐 Security Features

- ✅ SQL Injection Prevention (PDO Prepared Statements)
- ✅ XSS Protection (HTML Sanitization)
- ✅ Session Management
- ✅ Role-Based Access Control (Admin, Cashier, Customer)
- ✅ Password Hashing
- ✅ HTTPS Support
- ✅ CSRF Protection (via tokens)

---

## 🤝 Contributing

Kami menerima kontribusi dari developer lain. Untuk berkontribusi:

1. **Fork repository** ini
2. **Buat branch** baru untuk fitur Anda:
   ```bash
   git checkout -b feature/AmazingFeature
   ```
3. **Commit changes** Anda:
   ```bash
   git commit -m 'Add some AmazingFeature'
   ```
4. **Push ke branch:**
   ```bash
   git push origin feature/AmazingFeature
   ```
5. **Buat Pull Request**

### **Coding Standards:**
- Follow PSR-12 PHP Standard
- Use meaningful variable names
- Add comments untuk complex logic
- Test sebelum submit PR

---

## 📝 Changelog

### **Version 1.0** (June 2026)
- Initial release
- Core features implemented
- Admin & Cashier panel
- Payment integration (Midtrans)
- Database migration

---

## 📄 License

Project ini dilisensikan di bawah **MIT License** - lihat file [LICENSE](LICENSE) untuk detail.

---

## 👨‍💻 Author

**Tirta Sanita Outbound Development Team**

---

## 🆘 Support & Troubleshooting

### **Dokumentasi:**
- 📖 [INSTALLATION.md](INSTALLATION.md) - Setup guide
- ⚡ [QUICKSTART.md](QUICKSTART.md) - Quick start
- 🐛 Troubleshooting section di INSTALLATION.md

### **Common Issues:**
- Database connection error → Lihat [INSTALLATION.md#troubleshooting](INSTALLATION.md#troubleshooting)
- Composer not found → Install Composer
- 404 error → Check folder structure dan web server path

### **Contact:**
- 📧 Email: support@tirtasanita.com
- 📱 WhatsApp: 0858-1077-1107
- 🐛 GitHub Issues: Buat issue baru

---

## 🎯 Roadmap

### **Future Improvements:**
- [ ] Mobile app (React Native)
- [ ] Advanced analytics dashboard
- [ ] Multi-language support
- [ ] API REST full documentation
- [ ] Rate limiting & caching
- [ ] Social media integration
- [ ] Review & rating system
- [ ] Loyalty program

---

## ✨ Highlights

✅ **Production Ready** - Tested & ready untuk production  
✅ **Easy to Setup** - Quick start dalam 5 menit  
✅ **Well Documented** - Dokumentasi lengkap & terstruktur  
✅ **Scalable** - Siap untuk growth  
✅ **Secure** - Best practices keamanan diterapkan  
✅ **Responsive** - Mobile friendly design  

---

## 📞 Quick Links

| Link | Deskripsi |
|------|-----------|
| [INSTALLATION.md](INSTALLATION.md) | Panduan instalasi detail |
| [QUICKSTART.md](QUICKSTART.md) | Mulai dalam 5 menit |
| [admin/CASHIER_WORKFLOW.md](admin/CASHIER_WORKFLOW.md) | Workflow cashier |
| [Database SQL](database/tirtasanita_db.sql) | Database schema |

---

## 🙏 Terima Kasih

Terima kasih telah menggunakan **Tirta Sanita Outbound Platform**!

Jika Anda menemukan bug atau memiliki saran, silakan buat issue atau hubungi kami.

---

**Made with ❤️ by Tirta Sanita Outbound Development Team**

---

**Last Updated:** June 2026  
**Version:** 1.0  
**Status:** Active Development  

⭐ Jika project ini membantu, silakan beri bintang di GitHub! ⭐
