# 🛒 Laravel Stripe Product & Notification API

A backend-only **Laravel API project** that demonstrates how to build a real-world SaaS-style system with:

- Product management
- One-time payments using **Stripe Checkout**
- Secure payment verification via **Stripe Webhooks**
- Order management
- User notifications (in-app + email)
- Targeted offer notifications sent by admin
- Sanctum authentication

This project is designed for **learning and production-level architecture**.

---

## 🚀 Features

### 🔐 Authentication
- Laravel Sanctum API authentication
- Token-based access for users and admin

### 📦 Products
- Create and manage products
- Price stored in **cents** (Stripe-compatible)
- Enable / disable products
- Soft delete support

### 💳 Payments (Stripe)
- One-time payments using Stripe Checkout
- Secure webhook verification
- Prevents fake or client-side payment confirmation

### 📑 Orders
- Track all purchase attempts
- Payment status: `pending`, `paid`, `failed`
- Linked to user and product

### 🔔 Notifications
- Payment success notification (in-app + email)
- Offer / promotion notifications
- Targeted notifications (send to selected users)
- Mark notifications as read
- Fetch all / unread notifications via API

### 📧 Email
- SMTP-based email notifications
- Offer emails sent together with in-app notifications
- Queue-ready design

---

## 🧱 Tech Stack

- **Laravel** (API-only)
- **Laravel Sanctum** – authentication
- **Stripe PHP SDK**
- **MySQL** (or any supported DB)
- **Laravel Notifications**
- **Stripe CLI** (for webhook testing)
- **Postman** (API testing)

---

## 📂 Database Tables

- `users`
- `products`
- `orders`
- `notifications`

---

## ⚙️ Installation

### 1️⃣ Clone repository
```bash
git clone https://github.com/faruk881/payonce.git
cd payonce
