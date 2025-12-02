<img width="939" height="428" alt="image" src="https://github.com/user-attachments/assets/b9c783dc-fc1c-4fd7-a7cb-455cf19e2187" />
<img width="751" height="417" alt="image" src="https://github.com/user-attachments/assets/6758a2fd-1e99-4f3a-b684-f0fb76694762" />


# 🍔 POS Kasir - Admin Dashboard

A complete, production-ready **Point of Sale (POS) Admin Dashboard** with real-time analytics, menu management, and order tracking. Built with **Laravel 11**, **Tailwind CSS**, and **Chart.js**.

## ✨ Features

### 📊 Dashboard Analytics
- **Real-time Statistics**: Total revenue, orders, average sale, and discounts
- **Sales Chart**: 30-day sales trends with interactive line chart
- **Payment Distribution**: Pie chart showing payment method breakdown
- **Popular Menu**: Table of best-selling menu items with sales data

### 🍽️ Menu Management
- ✅ Create new menu items with categories (Burger, Pasta, Pizza, Drink, Dessert)
- ✅ Edit existing menu information (name, price, description, image)
- ✅ Delete menu items with automatic database cleanup
- ✅ Track sold quantity per item
- ✅ Paginated menu listing with search capability

### 📋 Order Management
- ✅ Create orders with multiple items and dynamic calculations
- ✅ Apply discounts per item with real-time total updates
- ✅ Multiple payment methods (Cash, Credit Card, QRIS)
- ✅ Order status tracking (Pending, Completed, Cancelled)
- ✅ Edit and delete orders with full history
- ✅ View detailed order items and pricing

### 🎨 User Interface
- ✅ Responsive Tailwind CSS design
- ✅ Modern navigation sidebar with active states
- ✅ Form validation with error messages
- ✅ Success/error flash notifications
- ✅ Mobile-friendly responsive layout

### 💾 Database
- ✅ Normalized relational schema
- ✅ Foreign key relationships with cascading deletes
- ✅ Proper indexing for performance
- ✅ Sample data seeding (5 menus, 20 orders)

---

## 🚀 Quick Start

### Prerequisites
- PHP 8.1+
- MySQL 5.7+
- Composer
- Node.js (optional)

### Installation

1. **Navigate to project directory**
   ```bash
   cd "c:\xampp\htdocs\POS-Kasir"
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Setup environment**
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```

4. **Configure database** (edit `.env`)
   ```
   DB_DATABASE=pos_kasir
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Create database and run migrations**
   ```bash![WhatsApp Image 2025-11-27 at 18 30 40](https://github.com/user-attachments/assets/57bbf2ba-0e8d-43c8-9a16-0830cec90bf3)

   mysql -u root -e "CREATE DATABASE pos_kasir;"
   php artisan migrate:fresh --seed
   ```

6. **Start development server**
   ```bash
   php artisan serve
   ```

7. **Access application**
   ```
   http://localhost:8000
   ```

---

## 📁 Project Structure

```
POS-Kasir/
│
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php     (Analytics & stats)
│   │   ├── MenuController.php          (Menu CRUD)
│   │   └── OrderController.php         (Order CRUD)
│   │
│   └── Models/
│       ├── Menu.php                    (Menu model)
│       ├── Order.php                   (Order model)
│       ├── OrderItem.php               (Order item model)
│       └── Transaction.php             (Payment model)
│
├── database/
│   ├── migrations/
│   │   ├── 2024_01_01_000003_create_menus_table.php
│   │   ├── 2024_01_01_000004_create_orders_table.php
│   │   ├── 2024_01_01_000005_create_order_items_table.php
│   │   └── 2024_01_01_000006_create_transactions_table.php
│   │
│   └── seeders/
│       └── DatabaseSeeder.php          (Sample data)
│
├── resources/views/
│   ├── layouts/app.blade.php           (Main layout)
│   ├── dashboard/index.blade.php       (Dashboard)
│   ├── menus/                          (Menu views)
│   └── orders/                         (Order views)
│
├── routes/web.php                      (Web routes)
├── SETUP_GUIDE.md                      (Detailed setup)
├── ERD_DOCUMENTATION.md                (Database schema)
└── API_DOCUMENTATION.md                (API specs)
```

---

## 🌐 Routes & Endpoints

### Dashboard
| Method | Route | Description |
|--------|-------|-------------|
| GET | `/` | Dashboard page |
| GET | `/api/chart-data` | Sales chart data (JSON) |
| GET | `/api/payment-data` | Payment data (JSON) |

### Menus (RESTful)
| Method | Route | Description |
|--------|-------|-------------|
| GET | `/menus` | Menu list |
| POST | `/menus` | Create menu |
| PUT | `/menus/{id}` | Update menu |
| DELETE | `/menus/{id}` | Delete menu |

### Orders (RESTful)
| Method | Route | Description |
|--------|-------|-------------|
| GET | `/orders` | Order list |
| POST | `/orders` | Create order |
| PUT | `/orders/{id}` | Update order |
| DELETE | `/orders/{id}` | Delete order |

---

## 💾 Database Schema

### Relationships
```
MENUS (1:N)→ ORDER_ITEMS
ORDER_ITEMS (N:1)→ ORDERS
ORDERS (1:1)→ TRANSACTIONS
```

### Key Tables

**MENUS**
- Stores menu items with price, category, description
- Tracks sold_quantity for popular menu calculations

**ORDERS**
- Stores order headers with total amounts and payment method
- Status tracking: pending, completed, cancelled

**ORDER_ITEMS**
- Junction table linking orders to menu items
- Stores quantity, price, and discount per item

**TRANSACTIONS**
- Payment details for each order
- Tracks payment method, amount paid, change, and status

See `ERD_DOCUMENTATION.md` for complete schema details.

---

## 📊 Sample Data

The seeder automatically populates:

### Menus (5 items)
| Name | Category | Price |
|------|----------|-------|
| Cheese Burger | Burger | Rp 35,000 |
| Pasta Bolognese | Pasta | Rp 45,000 |
| Pepperoni Pizza | Pizza | Rp 65,000 |
| Iced Tea | Drink | Rp 12,000 |
| Chocolate Cake | Dessert | Rp 28,000 |

### Orders (20 orders)
- Random payment methods (Cash, Credit Card, QRIS)
- 1-3 items per order
- 0-15% discount per item
- Distributed across last 30 days

---

## 🛠️ Technology Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 11 |
| Database | MySQL 5.7+ |
| Frontend | Blade Templates |
| Styling | Tailwind CSS |
| Charts | Chart.js |
| Icons | Font Awesome |

---

## 📚 Documentation

- **SETUP_GUIDE.md** - Detailed installation and configuration
- **ERD_DOCUMENTATION.md** - Database schema and relationships
- **API_DOCUMENTATION.md** - API responses and data flow

---

## ✅ Testing

### Verify Installation
```bash
php artisan route:list
php artisan tinker
>>> App\Models\Menu::count()
5
>>> App\Models\Order::count()
20
```

---

## 🔐 Security Features

✅ CSRF Protection on all forms  
✅ Mass assignment protection on models  
✅ Server-side input validation  
✅ SQL injection prevention via Eloquent ORM  
✅ Proper error handling  

---

## 📈 Performance

- Database indexing on frequently queried fields
- Eager loading relationships
- Pagination for large datasets
- JSON API endpoints for charts

---

## 🎉 Quick Commands

```bash
php artisan serve              # Start server
php artisan migrate:fresh --seed  # Fresh setup
php artisan cache:clear        # Clear caches
php artisan route:list         # Show routes
```

---

**Status**: ✅ Production Ready  
**Version**: 1.0.0  
**Laravel**: 11  
**PHP**: 8.1+

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
