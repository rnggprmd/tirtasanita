# 👤 User Account Setup Guide

**Status:** ✅ SEEDER CREATED  
**Date:** June 26, 2026

---

## 📋 Overview

Sistem Tirta Sanita Outbound mendukung 3 user roles:

| Role | Purpose | Access |
|------|---------|--------|
| **Admin** | System management & operations | Full access |
| **Cashier** | POS & instant ticket sales | Limited admin + POS |
| **User** | Customer account (optional) | Public website + My account |

Dokumentasi ini menjelaskan setup untuk **User** (Customer) account.

---

## ✅ Default User Account

User default sudah ditambahkan ke database:

```
ID:       3
Name:     Naya
WhatsApp: 081234567891011
Email:    naya@tirtasanita.com
Password: naya123
Role:     user
```

---

## 🔐 User Login

### **Login Page:**
```
http://localhost/tirtasanita (Homepage with login option)
```

### **User Credentials:**
```
WhatsApp: 081234567891011
Password: naya123
```

### **After Login:**
- Access user dashboard
- View own reservations
- Make online payments
- Download/print tickets
- Edit profile

---

## 🎯 User Features/Permissions

User account dapat mengakses:

### **Profile:**
- ✅ View profile information
- ✅ Update profile data
- ✅ Change password
- ✅ View activity history

### **Reservations:**
- ✅ Create new reservation
- ✅ View own reservations
- ✅ Cancel reservation (if allowed)
- ✅ Track status

### **Payments:**
- ✅ Online payment via Midtrans
- ✅ View payment history
- ✅ Download receipt
- ✅ Payment status tracking

### **Tickets:**
- ✅ Download ticket
- ✅ Print ticket
- ✅ Share ticket (email/WhatsApp)
- ✅ View ticket details

### **Notifications:**
- ✅ Email confirmations
- ✅ Payment reminders
- ✅ Status updates

---

## 📊 Comparison: All Roles

| Feature | Admin | Cashier | User |
|---------|-------|---------|------|
| System Access | Full | Limited | No |
| Dashboard | Full | Cashier | Own |
| Manage Packages | Yes | No | No |
| Manage Facilities | Yes | No | No |
| Manage Users | Yes | Limited | No |
| Create Reservation | Yes | Yes | Yes |
| Instant Ticket | No | Yes | No |
| Online Payment | No | No | Yes |
| View Own Data | Yes | Yes | Yes |
| Reports | Full | Limited | Own only |
| Settings | Full | Limited | Own |

---

## 🚀 How to Create User Accounts

### **Option 1: Automatic Seeder (RECOMMENDED)**

Use seeder script to create all default users:

**Buka di browser:**
```
http://localhost/tirtasanita/database/seeders.php
```

Script akan:
1. ✓ Delete existing users
2. ✓ Create Admin
3. ✓ Create Cashier
4. ✓ Create User (Naya)
5. ✓ Show verification
6. ✓ Display all credentials

### **Option 2: Manual User Registration**

Users dapat self-register di website (jika feature enabled):

1. Buka: http://localhost/tirtasanita
2. Klik "Register" atau "Sign Up"
3. Fill form:
   - Name
   - WhatsApp
   - Email
   - Password
   - Confirm Password
4. Submit

**Note:** Feature ini perlu di-implement terlebih dahulu

### **Option 3: Admin Create User**

Admin dapat membuat user account:

1. Login sebagai Admin
2. Go to: Admin > Users
3. Klik "Add User"
4. Fill form:
   - Name: (user name)
   - WhatsApp: (user phone)
   - Email: (user email)
   - Password: (set password)
   - Role: **User**
5. Save

### **Option 4: Direct SQL Insert**

```sql
-- Insert single user
INSERT INTO users (name, whatsapp, email, password, role) 
VALUES ('User Name', '081xxxxx', 'user@email.com', 'password123', 'user');

-- Insert multiple users
INSERT INTO users (name, whatsapp, email, password, role) VALUES 
('User 1', '081234567891', 'user1@email.com', 'pass123', 'user'),
('User 2', '081234567892', 'user2@email.com', 'pass123', 'user'),
('User 3', '081234567893', 'user3@email.com', 'pass123', 'user');
```

### **Option 5: Fresh Database Import**

Import fresh database dengan seeder data:

```bash
mysql -u root tirtasanita_db < database/tirtasanita_db.sql
```

---

## 🔐 User Workflow

```
1. Visit Homepage
   └─ http://localhost/tirtasanita

2. Browse Packages
   ├─ Filter by category
   ├─ View details
   └─ Check facilities

3. Create Reservation
   ├─ Select package
   ├─ Choose visit date
   ├─ Enter visitor count
   └─ Review price

4. Payment
   ├─ Select payment method
   ├─ Redirect to Midtrans
   └─ Complete payment

5. Get Ticket
   ├─ Receive email
   ├─ Download ticket
   ├─ Print ticket
   └─ Share with group

6. Visit Day
   ├─ Bring ticket
   ├─ Check in
   └─ Enjoy activities!
```

---

## 📱 User Dashboard Features

After login, user dapat mengakses:

### **Dashboard Overview:**
```
My Account
├─ Profile Info
├─ Contact Details
├─ Recent Activity
└─ Quick Actions

My Reservations
├─ Active Reservations
├─ Completed
├─ Cancelled
└─ Pending Payment

My Payments
├─ Transaction History
├─ Payment Status
├─ Receipts
└─ Invoices

My Tickets
├─ Download
├─ Print
└─ Share
```

---

## 🔄 Update User Information

User dapat update informasi pribadi:

**Via User Dashboard:**
1. Login dengan user account
2. Go to: Profile
3. Edit fields:
   - Name
   - Email
   - WhatsApp
   - Password
4. Save

---

## 🔒 Security Notes

### **Current (Development):**
⚠️ Passwords stored as plain text  
⚠️ No password hashing  
⚠️ No email verification  

### **For Production:**
🔴 Implement password hashing
```php
// Use password_hash() and password_verify()
$hashed = password_hash('naya123', PASSWORD_BCRYPT);
```

🔴 Add email verification on registration

🔴 Implement password reset flow

🔴 Add account activation

---

## 📊 Manage Multiple Users

### **Via Admin Panel:**
1. Login sebagai Admin
2. Go to: Admin > Users
3. View all users with roles
4. Edit/delete as needed

### **Via SQL Query:**
```sql
-- Get all users
SELECT id, name, whatsapp, email, role FROM users;

-- Get user stats
SELECT role, COUNT(*) as total FROM users GROUP BY role;

-- Update user
UPDATE users SET name='New Name', password='newpass' WHERE id=3;

-- Delete user
DELETE FROM users WHERE id=3;
```

---

## 🛠️ Troubleshooting

### **User can't login**

Check:
1. WhatsApp number correct?
2. Password correct?
3. Role is 'user'?
4. User exists in database?

**Verification:**
```sql
SELECT * FROM users WHERE whatsapp = '081234567891011';
```

### **User can't access dashboard**

Check:
1. User is logged in?
2. Session variables set?
3. PHP sessions enabled?

**Browser check:**
1. Press F12 > Application > Cookies
2. Check for session cookie
3. Verify session ID exists

### **Wrong features shown**

Check:
1. User role = 'user'?
2. Not 'admin' or 'cashier'?
3. Page checking role correctly?

---

## 📝 User Setup Checklist

- [x] Default user created (ID=3, Naya)
- [x] Seeder script updated
- [x] Documentation created
- [ ] Test user login
- [ ] Test user features
- [ ] Create additional users (if needed)
- [ ] Test payment flow
- [ ] Test ticket generation

---

## 🎯 Default Users Summary

### **1. Admin**
```
WhatsApp: 0812345678910
Password: admin123
Role:     admin
Access:   Full system
```

### **2. Cashier**
```
WhatsApp: 08123456789
Password: kasir123
Role:     cashier
Access:   POS + Limited admin
```

### **3. User (Naya)**
```
WhatsApp: 081234567891011
Password: naya123
Role:     user
Access:   Customer portal
```

---

## 🚀 Next Steps

1. **Run Seeder:**
   ```
   http://localhost/tirtasanita/database/seeders.php
   ```

2. **Test User Login:**
   - Go to: http://localhost/tirtasanita
   - Login with: WhatsApp `081234567891011` / Password `naya123`
   - Explore user dashboard

3. **Create Test Reservation:**
   - Browse packages
   - Create reservation
   - Complete payment (test)

4. **Download Ticket:**
   - View reservation
   - Download ticket
   - Verify email sent

5. **Create Additional Users:**
   - Via Admin panel
   - Or SQL insert
   - Test each user

---

## ✅ Verification

After seeder completes, verify all users created:

```bash
# Show all users
mysql -u root tirtasanita_db -e "SELECT id, name, whatsapp, role FROM users ORDER BY id;"
```

Expected output:
```
id | name  | whatsapp           | role
1  | Admin | 0812345678910      | admin
2  | Kasir | 08123456789        | cashier
3  | Naya  | 081234567891011    | user
```

---

## 📚 Related Documentation

- [LOGIN_TROUBLESHOOTING.md](LOGIN_TROUBLESHOOTING.md) - Login issues
- [CASHIER_USER_SETUP.md](CASHIER_USER_SETUP.md) - Cashier setup
- [DATABASE_SEEDERS.md](DATABASE_SEEDERS.md) - Seeder guide
- [INSTALLATION.md](INSTALLATION.md) - Installation

---

**Status:** ✅ USER ACCOUNT SETUP COMPLETE

**Last Updated:** June 26, 2026

