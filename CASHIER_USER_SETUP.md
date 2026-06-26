# 👥 Kasir (Cashier) User Setup Guide

**Status:** ✅ SEEDER CREATED  
**Date:** June 26, 2026

---

## 📋 Overview

Sistem Tirta Sanita Outbound mendukung multiple user roles:

- **Admin** - Full system access & management
- **Cashier** - POS system untuk instant ticket sales
- **User** - Regular customer (optional)

Dokumentasi ini menjelaskan setup untuk **Cashier** user.

---

## ✅ Default Cashier User

Kasir default sudah ditambahkan ke database:

```
ID:       2
Name:     Kasir
WhatsApp: 08123456789
Email:    kasir@tirtasanita.com
Password: kasir123
Role:     cashier
```

---

## 🚀 How to Create/Reset Cashier Users

### **Option 1: Automatic Seeder (RECOMMENDED)**

Gunakan seeder script untuk create/reset semua default users:

**Buka di browser:**
```
http://localhost/tirtasanita/database/seeders.php
```

Script akan:
1. ✓ Delete existing users
2. ✓ Create Admin user
3. ✓ Create Cashier user
4. ✓ Show verification
5. ✓ Display login credentials

### **Option 2: Manual Database Update**

Via phpMyAdmin:

1. Buka: http://localhost/phpmyadmin
2. Select database: `tirtasanita_db`
3. Open table: `users`
4. Insert new row:
   ```
   name:     Kasir
   whatsapp: 08123456789
   email:    kasir@tirtasanita.com
   password: kasir123
   role:     cashier
   ```

### **Option 3: SQL Query**

Di phpMyAdmin tab **SQL**:

```sql
-- Insert new cashier user
INSERT INTO users (name, whatsapp, email, password, role) 
VALUES ('Kasir', '08123456789', 'kasir@tirtasanita.com', 'kasir123', 'cashier');
```

### **Option 4: Fresh Database Import**

Import fresh database dengan seeder data included:

```bash
mysql -u root tirtasanita_db < database/tirtasanita_db.sql
```

---

## 🔐 Cashier Login

### **URL Admin Panel:**
```
http://localhost/tirtasanita/admin
```

### **Cashier Credentials:**
```
WhatsApp: 08123456789
Password: kasir123
```

### **After Login:**
- Sistem akan detect role: `cashier`
- Redirect ke: `cashier-dashboard.php`
- Show cashier-specific features

---

## 🎯 Cashier Features/Permissions

Kasir user dapat mengakses:

### **Dashboard:**
- ✅ Cashier-specific dashboard
- ✅ KPI display
- ✅ Quick actions

### **Transactions:**
- ✅ Create new reservation (walk-in)
- ✅ Instant ticket sales
- ✅ Process payments
- ✅ View transaction history

### **Management:**
- ✅ View reservations
- ✅ View payments
- ✅ View facilities
- ✅ View packages
- ✅ Limited user management

### **Reports:**
- ✅ Daily sales report
- ✅ Transaction report
- ✅ Ticket sales report

### **Operations:**
- ✅ Print ticket
- ✅ Send ticket (email/WhatsApp)
- ✅ Change password

---

## 📊 Comparison: Admin vs Cashier

| Feature | Admin | Cashier |
|---------|-------|---------|
| Dashboard | Full | Limited |
| Packages | CRUD | View only |
| Facilities | CRUD | View only |
| Reservations | Full | Own only |
| Payments | Full | Own only |
| Users | CRUD | Limited |
| Settings | Full | Limited |
| Instant Ticket | View | Create |
| Print Ticket | Yes | Yes |
| Send Ticket | Yes | Yes |
| Reports | Full | Limited |

---

## 🔄 Create Additional Cashier Users

Jika ingin menambah lebih banyak kasir:

### **Via Admin Panel:**
1. Login sebagai Admin
2. Go to: Admin > Users
3. Klik "Add User"
4. Fill form:
   - Name: (kasir name)
   - WhatsApp: (kasir phone)
   - Email: (kasir email)
   - Password: (set password)
   - Role: **Cashier**
5. Save

### **Via Database SQL:**
```sql
INSERT INTO users (name, whatsapp, email, password, role) 
VALUES 
('Kasir 2', '081234567890', 'kasir2@tirtasanita.com', 'kasir123', 'cashier'),
('Kasir 3', '081234567891', 'kasir3@tirtasanita.com', 'kasir123', 'cashier');
```

---

## 🔐 Security Notes

### **Current (Development):**
⚠️ Passwords stored as plain text  
⚠️ No password hashing  

### **For Production:**
🔴 Implement password hashing
```php
// Use password_hash() and password_verify()
$hashed = password_hash('kasir123', PASSWORD_BCRYPT);
```

Update: `admin/index.php` untuk verify hashed passwords

---

## 🛠️ Troubleshooting

### **Kasir can't login**

Check:
1. WhatsApp number correct?
2. Password correct?
3. Role is 'cashier'?
4. User exists in database?

**Verification:**
```sql
SELECT * FROM users WHERE whatsapp = '08123456789';
```

### **Kasir can't access cashier dashboard**

Check:
1. User role = 'cashier'?
2. Session variables set correctly?
3. PHP sessions enabled?

**Fix:**
```php
// Check in includes/functions.php
// Make sure session vars stored properly
$_SESSION["user_role"] = 'cashier';
```

### **Wrong dashboard shown**

Check `admin/index.php`:
```php
// Should redirect cashier to cashier-dashboard.php
if ($role === 'cashier') {
    redirect("cashier-dashboard.php");
}
```

---

## 📝 Cashier Setup Checklist

- [x] Default cashier user created (ID=2)
- [x] Seeder script ready: `database/seeders.php`
- [x] Cashier login enabled
- [x] Cashier dashboard created
- [x] Cashier features implemented
- [ ] Test cashier login
- [ ] Test cashier features
- [ ] Create additional cashiers (if needed)

---

## 🚀 Cashier Workflow

```
1. Cashier Login
   ├─ WhatsApp: 08123456789
   └─ Password: kasir123

2. Cashier Dashboard
   ├─ View today's sales
   └─ Quick actions

3. Create Reservation (Walk-in)
   ├─ Select package
   ├─ Enter visitor count
   └─ Calculate price

4. Process Payment
   ├─ Cash payment
   ├─ Midtrans payment
   └─ Update status

5. Generate Ticket
   ├─ Print ticket
   └─ Send via email/WhatsApp

6. View Reports
   ├─ Today sales
   ├─ Transaction history
   └─ Performance metrics
```

---

## 📚 Related Documentation

- [LOGIN_TROUBLESHOOTING.md](LOGIN_TROUBLESHOOTING.md) - Login issues & fixes
- [DATABASE_SEEDERS.md](DATABASE_SEEDERS.md) - Seeder documentation
- [INSTALLATION.md](INSTALLATION.md) - Installation guide
- [PROJECT_STRUCTURE.md](PROJECT_STRUCTURE.md) - Project architecture

---

## ✅ Verification

After seeder completes, verify kasir user created:

```bash
# In phpMyAdmin or MySQL command line
mysql -u root tirtasanita_db -e "SELECT * FROM users WHERE role='cashier';"
```

Expected output:
```
id | name | whatsapp    | email                  | password | role
2  | Kasir| 08123456789 | kasir@tirtasanita.com  | kasir123 | cashier
```

---

## 🎯 Next Steps

1. **Run Seeder:**
   ```
   http://localhost/tirtasanita/database/seeders.php
   ```

2. **Login as Cashier:**
   ```
   URL: http://localhost/tirtasanita/admin
   WhatsApp: 08123456789
   Password: kasir123
   ```

3. **Explore Cashier Dashboard:**
   - View available features
   - Test instant ticket creation
   - Try payment processing

4. **Create Additional Cashiers:**
   - Via Admin panel > Users
   - Or SQL insert

---

**Status:** ✅ CASHIER SETUP COMPLETE

**Last Updated:** June 26, 2026

