# 🛒 POS & Product Management System

A web-based **Point of Sale (POS) and Product Management System** designed to help businesses manage products, inventory, sales, reporting, and administrative operations.

## 🚀 Live Demo

🔗 **Live Demo:** https://pos-system-l7b1.onrender.com/

## 📌 Overview

This project is a full-stack business management application built with **Laravel**. It provides an administrative dashboard for managing products, inventory, sales, and business-related data through a centralized web interface.

The project was also prepared with production deployment in mind, including configuration for hosting platforms such as Render and Railway.

## ✨ Features

* 📊 Admin dashboard
* 🛍️ Product management
* 📦 Inventory management
* 💰 Point of Sale (POS)
* 📈 Sales and business reporting
* 👤 Admin authentication
* 🔐 Controlled user registration
* 🗄️ MySQL database integration
* 📱 Responsive web interface
* 🚀 Production deployment configuration

## 🛠️ Tech Stack

| Technology   | Usage                           |
| ------------ | ------------------------------- |
| Laravel      | Backend & application framework |
| PHP          | Server-side development         |
| MySQL        | Database                        |
| Tailwind CSS | UI styling                      |
| Vite         | Asset bundling                  |
| JavaScript   | Frontend interactions           |
| Docker       | Container support               |
| Git & GitHub | Version control                 |

## 📂 Project Structure

```text
POS-SYSTEM/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── tests/
├── Dockerfile
├── composer.json
├── package.json
├── render.yaml
├── railway.json
└── README.md
```

## ⚙️ Installation

### 1. Clone the repository

```bash
git clone https://github.com/kunvinthien-cyber/POS-SYSTEM.git
cd POS-SYSTEM
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install frontend dependencies

```bash
npm install
```

### 4. Configure environment

```bash
cp .env.example .env
```

Then configure your database settings in `.env`.

### 5. Generate application key

```bash
php artisan key:generate
```

### 6. Run migrations and seed database

```bash
php artisan migrate --seed
```

### 7. Start the development server

```bash
php artisan serve
```

In another terminal:

```bash
npm run dev
```

## 🔐 Production Notes

For production deployment:

* Set `APP_ENV=production`
* Set `APP_DEBUG=false`
* Configure a production MySQL database
* Keep administrator credentials in environment variables
* Never commit `.env` files or sensitive credentials
* Serve the Laravel application from the `public/` directory

## 🎯 Project Goals

This project was created to practice and demonstrate:

* Full-stack web application development
* Laravel application architecture
* Database-driven business applications
* CRUD operations
* Authentication and authorization
* Inventory and sales management
* Production deployment

## 👨‍💻 Author

**Kun Vinthien**

Junior Front-End Developer | Computer Science Graduate

* GitHub: https://github.com/kunvinthien-cyber
* Portfolio: https://thienweb.vercel.app
* LinkedIn: https://www.linkedin.com/in/kun-vinthien-830a1a404/

---

⭐ If you find this project useful, feel free to star the repository.
