# 📁 Struktur Project & Panduan Developer

**Tirta Sanita Outbound** - Sistem Manajemen Wisata Outbound

---

## 🏗️ Struktur Folder Project

```
tirtasanita/
├── 📁 admin/                      # Admin & Cashier Panel (40+ files)
│   ├── index.php                 # Login page
│   ├── dashboard.php             # Admin dashboard utama
│   ├── logout.php                # Logout handler
│   │
│   ├── # CASHIER PAGES
│   ├── cashier-dashboard.php     # Kasir dashboard
│   ├── cashier-add-reservation.php  # Tambah reservasi
│   ├── cashier-instant-ticket.php   # Instant ticket sales
│   ├── cashier-ticket-sales.php     # Laporan penjualan tiket
│   ├── cashier-ticket-detail.php    # Detail tiket
│   ├── cashier-print-ticket.php     # Print tiket
│   ├── cashier-send-ticket.php      # Kirim tiket via email/WA
│   ├── cashier-reservations.php     # Kelola reservasi kasir
│   ├── cashier-payments.php         # Kelola pembayaran
│   ├── cashier-facilities.php       # Lihat fasilitas
│   ├── cashier-packages.php         # Lihat paket
│   ├── cashier-users.php            # Kelola user terbatas
│   ├── cashier-settings.php         # Pengaturan kasir
│   │
│   ├── # ADMIN PAGES
│   ├── packages.php               # Manajemen paket
│   ├── package-add.php            # Tambah paket
│   ├── package-edit.php           # Edit paket
│   ├── package-facilities.php     # Kelola fasilitas paket
│   ├── package-categories.php     # Kategori paket
│   ├── facilities.php             # Manajemen fasilitas
│   ├── reservations.php           # Manajemen reservasi
│   ├── reservation-detail.php     # Detail reservasi
│   ├── payments.php               # Manajemen pembayaran
│   ├── payment-detail.php         # Detail pembayaran
│   ├── payment-methods.php        # Metode pembayaran
│   ├── users.php                  # Manajemen user
│   ├── user-edit.php              # Edit user
│   ├── user-reservations.php      # Reservasi user
│   ├── settings.php               # Pengaturan sistem
│   ├── profile.php                # Profile admin
│   ├── print-ticket.php           # Print tiket
│   │
│   ├── admin-style.css            # CSS admin panel
│   ├── sidebar-helper.php         # Navigation sidebar
│   ├── get-payment-method.php     # API metode pembayaran
│   └── CASHIER_WORKFLOW.md        # Workflow kasir
│
├── 📁 config/                     # Konfigurasi aplikasi
│   ├── database.php              # Database connection (PDO)
│   └── midtrans.php              # Midtrans payment config
│
├── 📁 includes/                   # Helper functions & components
│   ├── functions.php             # Helper functions
│   ├── navbar.php                # Navigation bar
│   ├── topbar.php                # Top bar
│   └── footer.php                # Footer
│
├── 📁 database/                   # Database files
│   └── tirtasanita_db.sql        # Full database schema & data
│
├── 📁 webhook/                    # Payment gateway webhooks
│   └── midtrans.php              # Midtrans webhook handler
│
├── 📁 css/                        # Stylesheets
│   ├── bootstrap.min.css         # Bootstrap 5 framework
│   └── style.css                 # Custom styles
│
├── 📁 js/                         # JavaScript files
│   └── main.js                   # Custom scripts
│
├── 📁 img/                        # Images & icons
│   ├── *.png, *.jpg              # Website images
│   └── icon/                     # Icon library (10 icons)
│
├── 📁 lib/                        # Third-party libraries
│   ├── animate/                  # Animation library
│   ├── counterup/                # Counter animation
│   ├── easing/                   # Easing effects
│   ├── lightbox/                 # Image lightbox
│   ├── owlcarousel/              # Image carousel
│   └── select2/                  # Select dropdown enhancement
│
├── 📁 vendor/                     # Composer packages
│   └── midtrans/                 # Midtrans payment SDK
│
├── 📁 uploads/                    # User uploads (created runtime)
│
├── 📄 index.php                   # Homepage (PUBLIC)
├── 📄 contact.php                 # Contact page (PUBLIC)
├── 📄 pricelist.php               # Price listing (PUBLIC)
├── 📄 CHECK_ADMIN_KASIR.php       # Auth check utility
├── 📄 404.html                    # 404 error page
│
├── 📄 composer.json               # Composer dependencies
├── 📄 composer.lock               # Composer lock file
├── 📄 .gitignore                  # Git ignore rules
│
├── 📄 INSTALLATION.md             # Installation guide
├── 📄 TECHNICAL_REVIEW.md         # Security & issues review
├── 📄 PROJECT_STRUCTURE.md        # This file
├── 📄 .env.example                # Environment template
└── 📄 README.md                   # Project overview

```

---

## 🌐 Entry Points

### **Public Access**
| URL | File | Purpose |
|-----|------|---------|
| `/` | `index.php` | Homepage & package listing |
| `/contact.php` | `contact.php` | Contact form |
| `/pricelist.php` | `pricelist.php` | Price information |

### **Admin & Cashier Access**
| URL | File | Purpose |
|-----|------|---------|
| `/admin/` | `admin/index.php` | Admin/Cashier login |
| `/admin/dashboard.php` | `admin/dashboard.php` | Admin main dashboard |
| `/admin/cashier-dashboard.php` | `admin/cashier-dashboard.php` | Cashier dashboard |

### **Payment & Webhooks**
| URL | File | Purpose |
|-----|------|---------|
| `/webhook/midtrans.php` | `webhook/midtrans.php` | Midtrans payment notifications |

---

## 💾 Database Tables

### **1. users**
```sql
- id (PK)
- name (varchar)
- whatsapp (unique, login key)
- email
- password (⚠️ plain text, needs hashing)
- role (admin, cashier, user)
- created_at
- updated_at
```
**Default Admin:** 08990559840 / 72onevi

### **2. packages**
```sql
- id (PK)
- category_id (FK)
- name
- description
- price_weekday
- price_weekend
- is_active
- created_at, updated_at
```

### **3. package_categories**
```sql
- id (PK)
- name
- description
- created_at, updated_at
```
**Categories:** Children, Young, Corporate, Youth Camp, Camping, Fishing

### **4. facilities**
```sql
- id (PK)
- name
- icon (FontAwesome class)
- description
- created_at, updated_at
```
**Count:** 19 facilities

### **5. package_facilities** (M-to-M)
```sql
- package_id (FK)
- facility_id (FK)
```

### **6. reservations**
```sql
- id (PK)
- user_id (FK)
- package_id (FK)
- visit_date
- num_visitors
- is_weekday
- total_price
- status (pending, confirmed, cancelled, completed)
- notes
- created_at, updated_at
```

### **7. payments**
```sql
- id (PK)
- reservation_id (FK)
- payment_method_id (FK)
- amount
- transaction_id (Midtrans)
- payment_date
- status (pending, completed, failed, refunded)
- created_at, updated_at
```

### **8. payment_methods**
```sql
- id (PK)
- name
- description
- type (bank_transfer, ewallet, qris, cash)
- account_info
- qr_image
- is_active
```
**Methods:** BCA, BNI, QRIS, GoPay, OVO, Dana

### **9. settings**
```sql
- id (PK)
- setting_key (unique)
- setting_value
```

---

## 📚 Helper Functions

Located in `includes/functions.php`

### **Authentication**
- `isLoggedIn()` - Check if user logged in
- `isAdmin()` - Check if user is admin
- `isCashier()` - Check if user is cashier
- `isStaff()` - Check if admin or cashier

### **Utilities**
- `redirect($location)` - Redirect to page
- `sanitize($input)` - Basic HTML sanitization
- `formatCurrency($amount)` - Format to Rupiah
- `sendEmail($to, $subject, $message)` - Send email

### **Pricing**
- `getPackagePrice($package, $date)` - Get price (weekday/weekend)
- `getDayType($date)` - Determine weekday/weekend
- `isWeekday($date)` - Check if weekday

### **Messaging**
- `setFlashMessage($name, $message, $class)` - Set flash message
- `displayFlashMessage()` - Display flash message

---

## 🔐 User Roles & Permissions

### **Admin**
- Full access ke semua menu
- Manage packages, facilities, categories
- Manage payment methods
- Manage all users
- View all reports
- System settings

### **Cashier**
- Cashier-specific dashboard
- Create reservations (instant ticket)
- Manage own reservations
- View payments
- Limited user management
- Print & send tickets
- View sales reports

### **User**
- Login (if implemented)
- View packages
- Create reservations
- View own reservations
- Make payments

---

## 🔄 Key Workflows

### **1. Reservation & Payment Flow**
```
User Browse Package
    ↓
Add to Cart / Create Reservation
    ↓
Enter Personal Data
    ↓
Select Payment Method
    ↓
[If Online Payment (Midtrans)]
    ↓
Redirect to Midtrans Payment
    ↓
Midtrans Webhook Updates Status
    ↓
[Email & WhatsApp Confirmation]
    ↓
Payment Verified
    ↓
Ticket Generated
```

### **2. Admin Management Flow**
```
Admin Login (WhatsApp + Password)
    ↓
Dashboard (View Statistics)
    ↓
Manage: Packages, Facilities, Payments, Users, Settings
    ↓
View Reports
    ↓
Logout
```

### **3. Cashier POS Flow**
```
Cashier Login (WhatsApp + Password)
    ↓
Cashier Dashboard
    ↓
Add New Reservation (Walk-in Customer)
    ↓
Select Package & Facilities
    ↓
Enter Customer Data
    ↓
Process Payment (Manual or Midtrans)
    ↓
Print Ticket
    ↓
Send Ticket (Email/WhatsApp)
```

---

## 🎯 Feature List

### **Public Website Features**
- ✅ Homepage dengan package showcase
- ✅ Package filtering by category
- ✅ Facility listing
- ✅ Contact form
- ✅ Responsive design (Bootstrap 5)
- ✅ Image gallery (Lightbox)
- ✅ Carousel/Slider (Owl Carousel)

### **Admin Panel Features**
- ✅ Dashboard with statistics
- ✅ Package management (CRUD)
- ✅ Facility management (CRUD)
- ✅ Payment method management
- ✅ User management (CRUD)
- ✅ Reservation management
- ✅ Payment tracking
- ✅ System settings
- ✅ User profile

### **Cashier Panel Features**
- ✅ Cashier dashboard (KPI display)
- ✅ Instant ticket sales (walk-in)
- ✅ Reservation management
- ✅ Payment processing
- ✅ Ticket printing
- ✅ Ticket sending (email/WA)
- ✅ Sales reporting

### **Payment Features**
- ✅ Multiple payment methods (6 types)
- ✅ Midtrans integration (online payment)
- ✅ Payment status tracking
- ✅ Transaction history
- ✅ Receipt generation

### **Notification Features**
- ✅ Email confirmation (uses PHP mail)
- ⏳ WhatsApp integration (placeholder)
- ✅ SMS integration (requires API setup)

---

## 📊 Technology Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| **Backend** | PHP | 7.4+ |
| **Database** | MySQL/MariaDB | 5.7+ |
| **Frontend** | HTML5 + Bootstrap | 5.0+ |
| **JavaScript** | jQuery | 3.4+ |
| **Payment Gateway** | Midtrans SDK | 2.6+ |
| **Web Server** | Apache | 2.4+ |
| **Package Manager** | Composer | Latest |

---

## 🔧 Development Guidelines

### **Code Style**
- Procedural PHP (follow existing pattern)
- Use prepared statements for database queries
- Use PDO for database connection
- Follow camelCase for functions/variables
- Use SNAKE_CASE for database fields

### **Database**
- Always use prepared statements
- Use transactions for critical operations
- Add timestamps (created_at, updated_at)
- Use appropriate data types
- Add indexes on frequently queried fields

### **Security**
- Validate all user inputs
- Sanitize output in HTML
- Use password hashing (implement)
- Add CSRF tokens to forms (implement)
- Use HTTPS in production

### **File Organization**
- Admin pages in `admin/` folder
- Public pages in root
- Reusable code in `includes/`
- Configuration in `config/`
- Static assets in `css/`, `js/`, `img/`, `lib/`

---

## 🚀 Deployment Checklist

Before deploying to production:

- [ ] Change default admin password
- [ ] Enable password hashing
- [ ] Configure `.env` with production values
- [ ] Update Midtrans to production keys
- [ ] Setup email configuration
- [ ] Enable HTTPS
- [ ] Add CSRF token protection
- [ ] Add session security headers
- [ ] Setup error logging
- [ ] Backup database
- [ ] Test payment workflow
- [ ] Test email/WhatsApp notifications
- [ ] Performance testing
- [ ] Security audit

---

## 📖 Additional Documentation

- **Installation:** See `INSTALLATION.md`
- **Security Issues:** See `TECHNICAL_REVIEW.md`
- **Cashier Workflow:** See `admin/CASHIER_WORKFLOW.md`

---

**Last Updated:** June 26, 2026  
**Version:** 1.0

