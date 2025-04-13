<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## 🛒 Laravel 12 RESTful E-Commerce API

This is a full-featured RESTful API for an e-commerce platform, built using **Laravel 12**. It includes product listing, cart, apply discount coupon, checkout, and integrated payment using **Midtrans**.

---

## 🚀 Features

- 📦 Product listing with categories & stock
- 🛒 Cart management (add, update, remove)
- 🧾 Order processing with invoice generation
- 💳 Midtrans payment gateway integration
- 🧑‍💼 Role-based authentication (Admin, Customer)
- 📑 Auto-generated API documentation (via Scramble)

---

## ⚙️ Tech Stack

- **Framework**: Laravel 12
- **Database**: MySQL / MariaDB
- **Authentication**: Sanctum
- **Payment**: Midtrans
- **Documentation**: Laravel Scramble (HTML static)

---

## 🧪 Requirements

- PHP >= 8.2
- Composer
- MySQL
- Midtrans Account (sandbox)

---

## 🔧 Installation

### 1. Clone Project & Install Dependencies
```bash
git clone https://github.com/yourusername/ecommerce-api.git
cd ecommerce-api
composer install
cp .env.example .env
php artisan key:generate
```

### 2. Setup your .env
```bash
APP_NAME=LaravelEcommerce
APP_URL=http://localhost:8000

DB_DATABASE=your_db
DB_USERNAME=your_user
DB_PASSWORD=your_password

MIDTRANS_MERCHANT_ID=
MIDTRANS_CLIENT_KEY=
MIDTRANS_SERVER_KEY=
```

### 3. Run migration and seed
```bash
php artisan migrate --seed
```

---

## 🙌 Credits

- Laravel 12
- Midtrans
- Scramble
- Postman

---

## 📬 API Documentation
https://ecommerce-api.inovasicode.com/public/documentation-api#/


