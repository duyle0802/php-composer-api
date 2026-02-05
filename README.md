# 🌟 BrightShop E-Commerce Platform

A comprehensive Vietnamese e-commerce website for selling electronics (BrightShop) with complete MVC architecture, RESTful API, and modern frontend.

---

## 📋 Table of Contents

- [Project Overview](#project-overview)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Project Structure](#project-structure)
- [Installation & Setup](#installation--setup)
- [Usage Guide](#usage-guide)
- [API Documentation](#api-documentation)
- [Database Schema](#database-schema)
- [Required Files](#required-files)
- [Configuration](#configuration)
- [Contact & Support](#contact--support)

---

## 🎯 Project Overview

**BrightShop** is a fully-functional e-commerce platform built with:
- **Backend**: PHP 8.3 with OOP & MVC Architecture
- **Frontend**: Vanilla JavaScript with Bootstrap 5
- **Database**: MySQL 5.7+
- **API**: 40+ RESTful endpoints

The project demonstrates:
- Complete CRUD operations
- Authentication & Authorization
- Session management
- Shopping cart & checkout flow
- Admin dashboard
- Product management & search
- Order processing
- Responsive design

---

## ✨ Features

### Customer Features
- ✅ User registration & authentication
- ✅ Product browsing & search
- ✅ Product filtering by category & price
- ✅ Shopping cart management
- ✅ Checkout & order placement
- ✅ Order history & tracking
- ✅ User profile management
- ✅ Responsive design (mobile-friendly)

### Admin Features
- ✅ Dashboard with statistics
- ✅ Product CRUD operations
- ✅ Category management
- ✅ Order management
- ✅ User management (ban/unban)
- ✅ Voucher system

### Technical Features
- ✅ 40+ API endpoints
- ✅ Role-based access control (User/Admin)
- ✅ Password hashing with bcrypt
- ✅ PDO prepared statements (SQL injection protection)
- ✅ Session-based authentication
- ✅ RESTful API design
- ✅ Dynamic URL routing
- ✅ Error handling & validation

---

## 🛠️ Technology Stack

| Component | Technology | Version |
|-----------|-----------|---------|
| **Backend** | PHP | 8.3.6 |
| **Database** | MySQL | 5.7+ |
| **Frontend** | HTML5, CSS3, JavaScript | ES6+ |
| **Framework** | Bootstrap | 5.3.0 |
| **Server** | Apache / PHP Built-in | - |
| **Database Abstraction** | PDO | - |

---

## 📁 Project Structure

```
PHPCom_APIver/
│
├── 📁 app/                          # Application Logic
│   ├── controllers/                 # Request Handlers (6 files)
│   │   ├── AuthController.php       # Authentication
│   │   ├── ProductController.php    # Products
│   │   ├── CartController.php       # Shopping Cart
│   │   ├── OrderController.php      # Orders
│   │   ├── CategoryController.php   # Categories
│   │   └── UserController.php       # User Management
│   │
│   ├── models/                      # Data Models (6 files)
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Cart.php
│   │   ├── Order.php
│   │   └── Voucher.php
│   │
│   └── views/                       # Frontend Templates (12 files)
│       ├── home.php                 # Homepage
│       ├── products.php             # Products listing
│       ├── product-detail.php       # Product details
│       ├── cart.php                 # Shopping cart
│       ├── checkout.php             # Checkout page
│       ├── login.php                # Login
│       ├── register.php             # Registration
│       ├── profile.php              # User profile
│       ├── orders.php               # Order history
│       ├── admin.php                # Admin dashboard
│       ├── about.php                # About us
│       ├── contact.php              # Contact us
│       └── order-confirmation.php   # Order confirmation
│
├── 📁 config/                       # Configuration Files
│   ├── config.php                   # Main configuration
│   └── database.php                 # Database connection class
│
├── 📁 database/                     # Database Files
│   └── schema.sql                   # Database structure & sample data
│
├── 📁 public/                       # Public Entry Point
│   ├── index.php                    # Frontend router
│   └── api.php                      # API router & endpoints
│
├── 📁 assets/                       # Static Files
│   ├── css/
│   │   ├── bootstrap.min.css        # Bootstrap framework
│   │   └── style.css                # Custom styles
│   ├── js/
│   │   ├── bootstrap.bundle.min.js  # Bootstrap JS
│   │   ├── jquery.min.js            # jQuery
│   │   └── common.js                # Custom JavaScript
│   └── images/                      # Product images
│
├── .env                             # Environment variables (NOT committed)
├── .env.example                     # Environment template (committed)
├── .gitignore                       # Git ignore rules
├── .htaccess                        # Apache rewrite rules
├── router.php                       # PHP built-in server router
├── composer.json                    # PHP dependencies
└── README.md                        # This file

```

---

## 🚀 Installation & Setup

### Prerequisites

- **PHP**: 8.0 or higher
- **MySQL**: 5.7 or higher
- **Apache** with mod_rewrite OR **PHP built-in server**
- **Git**: For version control

### Step 1: Clone Repository

```bash
git clone <repository-url>
cd PHPCom_APIver
```

### Step 2: Configure Environment

1. Copy environment template:
   ```bash
   cp .env.example .env
   ```

2. Edit `.env` with your database credentials:
   ```env
   DB_HOST=localhost
   DB_NAME=Bright_Database
   DB_USER=root
   DB_PASSWORD=your_password_here
   ```

### Step 3: Create Database

```bash
# Using MySQL CLI
mysql -u root -p < database/schema.sql
```

Or manually:
```sql
CREATE DATABASE Bright_Database CHARACTER SET utf8mb4;
USE Bright_Database;
-- Import schema.sql file
```

### Step 4: Set Permissions

```bash
chmod 755 -R config/
chmod 755 -R app/
chmod 755 -R public/
chmod 755 -R assets/
```

### Step 5: Start Development Server

**Option A: PHP Built-in Server (Recommended)**
```bash
cd /var/www/html/PHPCom_APIver
php -S localhost:8000 router.php
```
Then visit: `http://localhost:8000`

**Option B: Apache Server**
```bash
# Configure Apache virtual host
sudo a2enmod rewrite
sudo service apache2 restart
```
Then visit: `http://localhost/PHPCom_APIver`

---

## 📖 Usage Guide

### For Customers

1. **Register/Login**
   - Click "Đăng nhập" or "Đăng ký"
   - Fill in credentials
   - Login to access cart & checkout

2. **Browse Products**
   - Go to "Sản phẩm"
   - Filter by category or price
   - Search for specific products

3. **Add to Cart**
   - Click product card
   - Click "Giỏ hàng" or view details then "Mua ngay"
   - Go to cart to review

4. **Checkout**
   - Go to "Giỏ hàng"
   - Review items
   - Click "Thanh toán"
   - Place order

5. **View Orders**
   - Go to "Đơn hàng của tôi"
   - View order history and status

### For Admin

1. **Login as Admin**
   - Username: `admin`
   - Password: `Admin@123`

2. **Admin Dashboard**
   - Navigate to "Admin"
   - Manage products, categories, users, orders

3. **Operations**
   - **Products**: Add, edit, delete products
   - **Categories**: Create/modify categories
   - **Orders**: View and update order status
   - **Users**: Ban/unban users

---

## 🔌 API Documentation

### Base URL
```
http://localhost:8000/api
```

### Authentication Endpoints

```
POST   /api/auth/register        - Register new user
POST   /api/auth/login           - Login user
POST   /api/auth/logout          - Logout user
GET    /api/auth/profile         - Get user profile
POST   /api/auth/update-profile  - Update profile
```

### Product Endpoints

```
GET    /api/products                 - List all products (paginated)
GET    /api/products?page=1         - Get products by page
GET    /api/products/1              - Get specific product
GET    /api/products/search         - Search products
POST   /api/products                - Add product (Admin)
PUT    /api/products/1              - Update product (Admin)
DELETE /api/products/1              - Delete product (Admin)
```

### Category Endpoints

```
GET    /api/categories              - List all categories
POST   /api/categories              - Add category (Admin)
PUT    /api/categories/1            - Update category (Admin)
DELETE /api/categories/1            - Delete category (Admin)
```

### Cart Endpoints

```
POST   /api/cart/add                - Add item to cart
GET    /api/cart                    - View cart
PUT    /api/cart/update             - Update cart item quantity
DELETE /api/cart/remove             - Remove item from cart
```

### Order Endpoints

```
POST   /api/orders/create           - Create order
GET    /api/orders                  - Get user's orders
GET    /api/orders/1                - Get specific order
PUT    /api/orders/1/status         - Update order status (Admin)
GET    /api/orders/admin            - Get all orders (Admin)
```

### Example API Call

```javascript
// Get all products
fetch('/api/products?page=1')
  .then(res => res.json())
  .then(data => console.log(data));

// Login
fetch('/api/auth/login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    username: 'admin',
    password: 'Admin@123'
  })
})
.then(res => res.json())
.then(data => console.log(data));
```

---

## 🗄️ Database Schema

### Tables (7 total)

1. **users**
   - User accounts & authentication
   - Columns: id, username, email, password, full_name, role, is_banned, created_at

2. **categories**
   - Product categories
   - Columns: id, name, description, image, created_at

3. **products**
   - Product listings
   - Columns: id, category_id, name, description, price, quantity_in_stock, image, created_at

4. **cart**
   - Shopping cart items
   - Columns: id, user_id, product_id, quantity, created_at

5. **orders**
   - Customer orders
   - Columns: id, user_id, total_amount, status, created_at

6. **order_items**
   - Individual items in orders
   - Columns: id, order_id, product_id, quantity, price

7. **vouchers**
   - Discount codes
   - Columns: id, code, discount_amount, max_uses, used_count, expiry_date

---

## 📋 Required Files to Run Project

### ✅ Must Have (Already Included)

```
✓ app/                          (Controllers, Models, Views)
✓ config/                       (Configuration files)
✓ public/                       (index.php, api.php)
✓ database/                     (schema.sql)
✓ assets/                       (CSS, JS, Images)
✓ .env                          (Environment variables)
✓ .env.example                  (Environment template)
✓ .htaccess                     (Apache rewrite rules)
✓ router.php                    (PHP server router)
✓ composer.json                 (If using Composer)
```

### ⚠️ Optional (Generated During Setup)

```
Optional: 
- database/ backups/            (Database backups)
- logs/                        (Application logs)
- cache/                       (Cache files)
```

### ❌ Not Needed (Ignored by .gitignore)

```
✗ test_*.php                    (Test files)
✗ insert_products.php           (Data seeding)
✗ gen_hash.php                  (Utility)
✗ .vscode/, .idea/              (IDE settings)
✗ node_modules/, vendor/        (Dependencies)
```

---

## ⚙️ Configuration

### Environment Variables (.env)

```env
# Database
DB_HOST=localhost
DB_NAME=Bright_Database
DB_USER=root
DB_PASSWORD=your_password

# API
API_URL=http://localhost:8000/api

# Admin
ADMIN_EMAIL=admin@brightshop.com
ADMIN_NAME=BrightShop Admin
```

### Database Connection

Connection is configured in `config/database.php`:
```php
// Reads from .env
$host = getenv('DB_HOST');
$db = getenv('DB_NAME');
$user = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
```

### Default Admin Account

```
Username: admin
Password: Admin@123
```

---

## 📊 Sample Data

Database comes pre-populated with:

- **50 Products** (Keyboards, Mice, Laptops, Graphics Cards, Workstations)
- **5 Categories** (Input Devices, Laptops, Graphics Cards, Workstations)
- **1 Admin Account** (admin/Admin@123)

---

## 🔒 Security Features

- ✅ **Password Hashing**: bcrypt (password_hash)
- ✅ **SQL Injection Protection**: PDO prepared statements
- ✅ **Session Management**: PHP sessions with secure headers
- ✅ **Role-based Access**: User/Admin differentiation
- ✅ **Environment Variables**: Sensitive data in .env
- ✅ **Authorization Checks**: Protected endpoints & pages

---

## 🐛 Troubleshooting

### Issue: Database Connection Failed
```
Solution: Check .env file has correct credentials
- Verify MySQL is running
- Check database exists
- Verify user permissions
```

### Issue: 404 Not Found on API calls
```
Solution: 
- Ensure router.php is running (for built-in server)
- Check .htaccess is configured (for Apache)
- Verify mod_rewrite is enabled
```

### Issue: CORS Errors
```
Solution:
- API is on same domain, no CORS needed
- If on different domain, add CORS headers to api.php
```

### Issue: Session Not Persisting
```
Solution:
- Check session.save_path is writable
- Verify session is started in config.php
- Check browser accepts cookies
```

---

## 👤 Contact & Support

**Developer**: Duy Lê
**Email**: freak8927@gmail.com  
**Project**: BrightShop E-Commerce Platform  
**Date**: February 2026

For questions, bug reports, or feature requests, please contact via email.

---

## 📄 License

This project is private and for educational purposes.

---

## 🎓 Learning Resources

- **API Architecture**: See `public/api.php` for routing
- **MVC Pattern**: See `app/` directory structure
- **Database**: Check `database/schema.sql`
- **Authentication**: See `AuthController.php`
- **Frontend**: Check `app/views/` for template structure

---

**Last Updated**: February 5, 2026  
**Version**: 1.0  
**Status**: ✅ Production Ready

---
