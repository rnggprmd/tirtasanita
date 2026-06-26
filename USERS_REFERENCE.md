# 👥 Users Reference Guide - All Accounts

**Status:** ✅ ALL USERS CONFIGURED  
**Date:** June 26, 2026

---

## 📊 Overview

Sistem memiliki 3 default user accounts dengan berbagai roles dan permissions:

| ID | Name | WhatsApp | Password | Role | Status |
|----|------|----------|----------|------|--------|
| 1 | Admin | 0812345678910 | admin123 | admin | ✅ Active |
| 2 | Kasir | 08123456789 | kasir123 | cashier | ✅ Active |
| 3 | Naya | 081234567891011 | naya123 | user | ✅ Active |

---

## 🔐 LOGIN CREDENTIALS

### **ADMIN PANEL:**
```
URL: http://localhost/tirtasanita/admin
```

**Admin Account:**
```
Role:     Administrator (Full System Access)
WhatsApp: 0812345678910
Password: admin123

Dashboard:     http://localhost/tirtasanita/admin/dashboard.php
Functions:     System management, user management, analytics
```

**Cashier Account:**
```
Role:     Cashier (POS System)
WhatsApp: 08123456789
Password: kasir123

Dashboard:     http://localhost/tirtasanita/admin/cashier-dashboard.php
Functions:     Instant tickets, reservations, payments
```

---

### **WEBSITE:**
```
URL: http://localhost/tirtasanita
```

**User Account:**
```
Role:     Regular User (Customer)
WhatsApp: 081234567891011
Password: naya123

Features:      Browse packages, reservations, payments, tickets
Access:        Public website + user dashboard
```

---

## 🎯 Role Permissions

### **Admin - Full Access**

**System:**
- Dashboard & analytics
- User management (CRUD)
- Settings & configuration

**Content:**
- Packages (create, read, update, delete)
- Facilities (create, read, update, delete)
- Categories (create, read, update, delete)
- Payment methods (CRUD)

**Transactions:**
- View all reservations
- View all payments
- Create reservations
- Manage status
- Generate reports

**Operations:**
- Print tickets
- Send notifications
- View logs
- System maintenance

---

### **Cashier - Limited Admin + POS**

**Dashboard:**
- Cashier-specific KPI
- Today's sales summary
- Quick actions

**Transactions:**
- Create instant reservations (walk-in)
- Process payments
- View own transactions
- Limited reports (today only)

**Tickets:**
- Print tickets
- Send tickets (email/WhatsApp)
- Reprint tickets

**Restrictions:**
- Cannot edit packages
- Cannot edit facilities
- Cannot create users
- Cannot access settings
- Cannot view other cashier's sales

---

### **User - Customer Only**

**Browse:**
- Browse all packages
- View package details
- Check facilities
- Gallery & information

**Account:**
- Create reservations
- View own reservations
- Track status
- Manage profile

**Payments:**
- Make online payments (Midtrans)
- View payment history
- Download receipts

**Tickets:**
- Download tickets
- Print tickets
- Share tickets

**Restrictions:**
- Cannot manage system
- Cannot view other users
- Cannot access admin panel
- Cannot see analytics

---

## 🚀 Quick Login Guide

### **For Admin (Full System Access):**
1. Go to: http://localhost/tirtasanita/admin
2. WhatsApp: `0812345678910`
3. Password: `admin123`
4. Click Login
5. View admin dashboard

### **For Cashier (POS System):**
1. Go to: http://localhost/tirtasanita/admin
2. WhatsApp: `08123456789`
3. Password: `kasir123`
4. Click Login
5. Auto-redirect to cashier dashboard

### **For User (Customer Portal):**
1. Go to: http://localhost/tirtasanita
2. WhatsApp: `081234567891011`
3. Password: `naya123`
4. Click Login
5. View user dashboard

---

## 📋 User Features by Role

### **Dashboard Access**

| Page | Admin | Cashier | User |
|------|-------|---------|------|
| Admin Dashboard | ✅ Yes | ❌ No | ❌ No |
| Cashier Dashboard | ❌ No | ✅ Yes | ❌ No |
| User Dashboard | ✅ View All | ✅ View Own | ✅ View Own |

### **Package Management**

| Action | Admin | Cashier | User |
|--------|-------|---------|------|
| Create Package | ✅ Yes | ❌ No | ❌ No |
| Edit Package | ✅ Yes | ❌ No | ❌ No |
| Delete Package | ✅ Yes | ❌ No | ❌ No |
| View Package | ✅ Yes | ✅ Yes | ✅ Yes |
| List Packages | ✅ Yes | ✅ Yes | ✅ Yes |

### **Reservation Management**

| Action | Admin | Cashier | User |
|--------|-------|---------|------|
| Create Reservation | ✅ Yes | ✅ Yes | ✅ Yes |
| View All | ✅ Yes | ❌ No | ❌ No |
| View Own | ✅ Yes | ✅ Yes | ✅ Yes |
| Edit | ✅ Yes | ✅ Own | ✅ Own |
| Delete | ✅ Yes | ✅ Own | ✅ Own |
| Change Status | ✅ Yes | ✅ Own | ❌ No |

### **Payment Management**

| Action | Admin | Cashier | User |
|--------|-------|---------|------|
| View All Payments | ✅ Yes | ❌ No | ❌ No |
| View Own Payments | ✅ Yes | ✅ Yes | ✅ Yes |
| Process Payment | ✅ Yes | ✅ Yes | ✅ Yes |
| Refund | ✅ Yes | ✅ Yes | ❌ No |

### **Ticket Operations**

| Action | Admin | Cashier | User |
|--------|-------|---------|------|
| Print | ✅ Yes | ✅ Yes | ✅ Yes |
| Send | ✅ Yes | ✅ Yes | ✅ Yes |
| Download | ✅ Yes | ✅ Yes | ✅ Yes |
| Reprint | ✅ Yes | ✅ Yes | ✅ Yes |

---

## 🔄 Create Additional Users

### **Method 1: Seeder Script**
```
http://localhost/tirtasanita/database/seeders.php
```
Resets all users to default 3 users

### **Method 2: Admin Panel**
1. Login as Admin
2. Go to: Users
3. Click "Add User"
4. Fill form with new data
5. Select role (admin/cashier/user)
6. Save

### **Method 3: SQL Insert**
```sql
-- Add new user
INSERT INTO users (name, whatsapp, email, password, role) 
VALUES ('User Name', '081xxxxx', 'email@domain.com', 'password', 'user');

-- Add multiple cashiers
INSERT INTO users (name, whatsapp, email, password, role) VALUES 
('Kasir 2', '081234567890', 'kasir2@email.com', 'pass123', 'cashier'),
('Kasir 3', '081234567891', 'kasir3@email.com', 'pass123', 'cashier');
```

### **Method 4: Command Line**
```bash
mysql -u root tirtasanita_db -e \
"INSERT INTO users (name, whatsapp, email, password, role) \
VALUES ('New User', '081xxxxx', 'user@email.com', 'pass123', 'user');"
```

---

## 🛠️ Manage Users

### **View All Users (Admin Only)**
Go to: Admin > Users

Or via SQL:
```sql
SELECT id, name, whatsapp, email, role FROM users ORDER BY id;
```

### **Edit User (Admin Only)**
1. Go to: Admin > Users
2. Click on user row
3. Edit fields
4. Save

Or via SQL:
```sql
UPDATE users SET password='newpass' WHERE id=3;
```

### **Delete User (Admin Only)**
1. Go to: Admin > Users
2. Click delete button
3. Confirm

Or via SQL:
```sql
DELETE FROM users WHERE id=3;
```

### **Change User Role (Admin Only)**
```sql
UPDATE users SET role='cashier' WHERE id=3;
```

---

## 🔐 Security Best Practices

### **Passwords:**
- ⚠️ Currently plain text (development only)
- 🔴 Production: Implement password hashing
- 🔴 Production: Use strong passwords

### **Access Control:**
- ✅ Role-based access implemented
- ⚠️ Session management basic
- 🔴 Production: Add 2FA

### **Data Protection:**
- ✅ PDO prepared statements (SQL injection safe)
- ⚠️ HTTPS not enabled
- 🔴 Production: Enable SSL/HTTPS

---

## 📱 User Workflows

### **Admin Workflow**
```
1. Login as Admin
2. Access Dashboard
3. View Analytics
4. Manage Users/Packages/Settings
5. Generate Reports
6. Logout
```

### **Cashier Workflow**
```
1. Login as Cashier
2. Access Cashier Dashboard
3. Create Walk-in Reservation
4. Select Package
5. Process Payment
6. Print Ticket
7. Send to Customer
8. Record Complete
```

### **User Workflow**
```
1. Visit Website
2. Browse Packages
3. Create Reservation
4. Complete Payment
5. Receive Ticket
6. Download/Print
7. Visit on Reserved Date
8. Check In
```

---

## ✅ Verification Commands

### **Check All Users**
```bash
mysql -u root tirtasanita_db -e "SELECT id, name, whatsapp, role FROM users;"
```

### **Count Users by Role**
```bash
mysql -u root tirtasanita_db -e "SELECT role, COUNT(*) FROM users GROUP BY role;"
```

### **Check Specific User**
```bash
mysql -u root tirtasanita_db -e "SELECT * FROM users WHERE whatsapp='0812345678910';"
```

### **Verify Database**
```bash
mysql -u root -e "SHOW DATABASES;" | grep tirtasanita
```

---

## 📞 Troubleshooting

### **Can't Login**
- Check WhatsApp number
- Check password
- Verify user exists: `SELECT * FROM users WHERE whatsapp='...';`
- Check role is correct

### **Wrong Dashboard**
- Verify role in database
- Clear browser cache
- Check session variables set correctly

### **Can't Create Users**
- Must be logged in as Admin
- Check database connection
- Verify users table exists

### **Permission Denied**
- Check user role
- Verify permissions set for role
- Check PHP code for permission checks

---

## 📚 Related Documentation

| Document | Purpose |
|----------|---------|
| [LOGIN_TROUBLESHOOTING.md](LOGIN_TROUBLESHOOTING.md) | Login issues & fixes |
| [CASHIER_USER_SETUP.md](CASHIER_USER_SETUP.md) | Cashier specific setup |
| [USER_ACCOUNT_SETUP.md](USER_ACCOUNT_SETUP.md) | User account setup |
| [INSTALLATION.md](INSTALLATION.md) | Installation guide |
| [database/seeders.php](database/seeders.php) | Seeder script |

---

## 🎯 Summary Table

| Item | Value |
|------|-------|
| **Total Default Users** | 3 |
| **Admin Accounts** | 1 |
| **Cashier Accounts** | 1 |
| **User Accounts** | 1 |
| **Seeder Script** | ✅ Available |
| **Auto-creation** | ✅ Yes |
| **Manual Add** | ✅ Yes |

---

## 🚀 Getting Started

1. **Import Database** (if fresh install):
   ```bash
   mysql -u root tirtasanita_db < database/tirtasanita_db.sql
   ```

2. **Run Seeder Script**:
   ```
   http://localhost/tirtasanita/database/seeders.php
   ```

3. **Test Admin Login**:
   ```
   URL: http://localhost/tirtasanita/admin
   WhatsApp: 0812345678910
   Password: admin123
   ```

4. **Test Cashier Login**:
   ```
   URL: http://localhost/tirtasanita/admin
   WhatsApp: 08123456789
   Password: kasir123
   ```

5. **Test User Login**:
   ```
   URL: http://localhost/tirtasanita
   WhatsApp: 081234567891011
   Password: naya123
   ```

---

**Status:** ✅ **ALL USERS READY**

**Last Updated:** June 26, 2026

