# Taman Kopses Ciseeng - Reservation System

A comprehensive reservation system for Taman Kopses Ciseeng that allows users to register, book tickets for various packages, and make payments. The system also includes an admin panel for managing packages, users, and reservations.

## Features

- **User Module**
  - User registration and login
  - Package browsing and selection
  - Reservation creation
  - Payment processing
  - E-ticket generation
  - Reservation history

- **Admin Module**
  - Dashboard with statistics
  - Package management (add, edit, delete)
  - User management
  - Reservation management
  - Payment verification

## Installation

1. **Prerequisites**
   - XAMPP (or any PHP server with MySQL)
   - PHP 7.4 or higher
   - MySQL 5.7 or higher

2. **Setup**
   - Clone or download this repository to your XAMPP htdocs folder
   - Create a MySQL database named `tkc_db`
   - Import the SQL file from `database/tkc_database.sql`
   - Configure the database connection in `config/database.php` if needed

3. **Default Admin Account**
   - WhatsApp: 085886863808
   - Password: password

## Directory Structure

```
tkc/
├── admin/                 # Admin module
├── assets/                # Static assets
├── config/                # Configuration files
├── database/              # Database SQL files
├── includes/              # Shared PHP components
├── uploads/               # User uploaded files
├── user/                  # User module
├── index.php              # Homepage
├── pricelist.php          # Package listing
└── contact.php            # Contact page
```

## Usage

1. **User Side**
   - Register a new account or login
   - Browse available packages
   - Select a package and date
   - Complete the reservation
   - Make payment
   - Receive e-ticket

2. **Admin Side**
   - Login to admin panel
   - Manage packages, categories, and facilities
   - View and manage reservations
   - Verify payments
   - Manage users

## Technologies Used

- PHP
- MySQL
- Bootstrap 5 (via CDN)
- Font Awesome (via CDN)
- jQuery
- HTML/CSS/JavaScript

## License

© 2025 Taman Kopses Ciseeng. All Rights Reserved.
