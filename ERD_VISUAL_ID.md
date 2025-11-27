# 🗂️ ENTITY RELATIONSHIP DIAGRAM (ERD) - VISUAL GUIDE

**Bahasa**: Bahasa Indonesia  
**Format**: Text-based diagram & SQL  
**Project**: POS-Kasir v1.0.0

---

## 📊 ERD Lengkap

### Diagram Visual

```
┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
┃                                                                        ┃
┃                       DATABASE: pos_kasir                             ┃
┃                                                                        ┃
┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛

┌──────────────────────────────┐
│         USERS (Login)        │
├──────────────────────────────┤
│ PK id (BIGINT)               │
│    name (VARCHAR)            │
│    email (VARCHAR) UNIQUE    │
│    email_verified_at (TS)    │
│    password (VARCHAR)        │
│    remember_token (VARCHAR)  │
│    role (ENUM) ◄── admin     │
│    created_at (TIMESTAMP)    │     "Setiap user bisa login"
│    updated_at (TIMESTAMP)    │
│    IndexS: email, role       │
└──────────────────────────────┘

┌──────────────────────────────┐
│    CATEGORIES (Kategori)     │
├──────────────────────────────┤
│ PK id (BIGINT)               │
│    name (VARCHAR)            │      "1 kategori bisa punya
│    slug (VARCHAR) UNIQUE     │       banyak menu"
│    description (TEXT)        │
│    created_at (TIMESTAMP)    │
│    updated_at (TIMESTAMP)    │
│    Index: slug               │
└──────────────────────────────┘
           │ (1:N)
           │ hasMany
           ▼
┌──────────────────────────────────────┐
│       MENUS (Menu Item)               │
├──────────────────────────────────────┤
│ PK id (BIGINT)                        │
│ FK category_id (BIGINT) ─────┐       │
│    name (VARCHAR)            │       │  "1 menu bisa ada di
│    description (TEXT)        │       │   banyak order items"
│    price (DECIMAL)           │       │
│    image_url (VARCHAR)       │       │
│    sold_quantity (INT) ◄─────┤───┐   │
│    created_at (TIMESTAMP)    │   │   │
│    updated_at (TIMESTAMP)    │   │   │
│    Index: category_id,       │   │   │
│            sold_quantity     │   │   │
└──────────────────────────────────────┘
           │ (1:N)
           │ hasMany
           ▼
┌──────────────────────────────────────────┐
│   ORDER_ITEMS (Detail Pesanan)           │
├──────────────────────────────────────────┤
│ PK id (BIGINT)                            │
│ FK order_id (BIGINT) ─────────┐           │  "Junction table
│ FK menu_id (BIGINT) ──────┐    │          │   linking orders
│    quantity (INT)         │    │          │   to menus"
│    price (DECIMAL)        │    │          │
│    discount (DECIMAL)     │    │          │
│    created_at (TIMESTAMP) │    │          │
│    updated_at (TIMESTAMP) │    │          │
│    Index: order_id,       │    │          │
│            menu_id        │    │          │
└──────────────────────────────────────────┘
           ▲        ▲
           │        │
    (N:1)  │        │  (N:1)
    ──────┘         └──── MENUS
           │
           │
┌──────────────────────────────────────────┐
│   ORDERS (Penjualan Header)              │
├──────────────────────────────────────────┤
│ PK id (BIGINT)                            │
│    order_number (VARCHAR) UNIQUE         │  "1 order punya
│    total_amount (DECIMAL)                 │   banyak items dan
│    total_quantity (INT)                   │   1 transaksi"
│    status (ENUM) ─┐                      │
│                   ├─ pending             │
│                   ├─ completed           │
│                   └─ cancelled           │
│    payment_method (ENUM) ─┐              │
│                            ├─ cash       │
│                            ├─ cc         │
│                            └─ qris       │
│    completed_at (TIMESTAMP)              │
│    created_at (TIMESTAMP)                │
│    updated_at (TIMESTAMP)                │
│    Index: created_at, status,            │
│            payment_method                │
└──────────────────────────────────────────┘
           │ (1:1)
           │ hasOne
           ▼
┌──────────────────────────────────────────┐
│   TRANSACTIONS (Pembayaran)              │
├──────────────────────────────────────────┤
│ PK id (BIGINT)                            │  "Detail pembayaran
│ FK order_id (BIGINT) UNIQUE              │   untuk setiap order"
│    payment_method (ENUM)                 │
│    amount_paid (DECIMAL)                 │
│    change_amount (DECIMAL)               │
│    status (ENUM) ─┐                      │
│                   ├─ pending             │
│                   ├─ success             │
│                   └─ failed              │
│    paid_at (TIMESTAMP)                   │
│    created_at (TIMESTAMP)                │
│    updated_at (TIMESTAMP)                │
│    Index: order_id, status               │
└──────────────────────────────────────────┘
```

---

## 📋 Penjelasan Setiap Table

### 1. USERS Table

**Tujuan**: Menyimpan data user untuk login & authorization

**Fields**:

| Field | Type | Constraint | Keterangan |
|-------|------|-----------|-----------|
| id | BIGINT | PK, AUTO_INCREMENT | Primary key |
| name | VARCHAR(255) | NOT NULL | Nama user |
| email | VARCHAR(255) | NOT NULL, UNIQUE | Email untuk login |
| email_verified_at | TIMESTAMP | NULL | Jika sudah verifikasi email |
| password | VARCHAR(255) | NOT NULL | Password di-hash (bcrypt) |
| remember_token | VARCHAR(100) | NULL | Token untuk "Remember Me" |
| role | ENUM('admin', 'cashier') | NOT NULL, DEFAULT='cashier' | Admin atau Cashier |
| created_at | TIMESTAMP | NULL | Waktu dibuat |
| updated_at | TIMESTAMP | NULL | Waktu terakhir update |

**Indexes**:
- `email` (UNIQUE): Untuk fast login lookup
- `role`: Untuk filter user berdasarkan role

**Data Contoh**:
```sql
INSERT INTO users VALUES
(1, 'Admin Demo', 'demo@example.com', NULL, '[bcrypt password]', NULL, 'admin', '2025-11-27 10:00:00', '2025-11-27 10:00:00'),
(2, 'Kasir Demo', 'cashier@example.com', NULL, '[bcrypt password]', NULL, 'cashier', '2025-11-27 10:00:00', '2025-11-27 10:00:00');
```

---

### 2. CATEGORIES Table

**Tujuan**: Menyimpan kategori menu (Burger, Pizza, Minuman, dll)

**Fields**:

| Field | Type | Constraint | Keterangan |
|-------|------|-----------|-----------|
| id | BIGINT | PK, AUTO_INCREMENT | Primary key |
| name | VARCHAR(255) | NOT NULL | Nama kategori |
| slug | VARCHAR(255) | NOT NULL, UNIQUE | URL-friendly version |
| description | TEXT | NULL | Deskripsi kategori |
| created_at | TIMESTAMP | NULL | Waktu dibuat |
| updated_at | TIMESTAMP | NULL | Waktu terakhir update |

**Indexes**:
- `slug` (UNIQUE): Untuk URL routing

**Data Contoh**:
```sql
INSERT INTO categories VALUES
(1, 'Burger', 'burger', 'Kategori hamburger', '2025-11-27 10:00:00', '2025-11-27 10:00:00'),
(2, 'Pizza', 'pizza', 'Kategori pizza', '2025-11-27 10:00:00', '2025-11-27 10:00:00'),
(3, 'Minuman', 'minuman', 'Kategori minuman', '2025-11-27 10:00:00', '2025-11-27 10:00:00');
```

---

### 3. MENUS Table

**Tujuan**: Menyimpan item menu yang dijual dengan harga & kategori

**Fields**:

| Field | Type | Constraint | Keterangan |
|-------|------|-----------|-----------|
| id | BIGINT | PK, AUTO_INCREMENT | Primary key |
| category_id | BIGINT | FK, NOT NULL | Foreign key ke categories |
| name | VARCHAR(255) | NOT NULL | Nama menu |
| description | TEXT | NULL | Deskripsi panjang |
| price | DECIMAL(10,2) | NOT NULL | Harga menu |
| image_url | VARCHAR(255) | NULL | Path gambar menu |
| sold_quantity | INT | UNSIGNED, DEFAULT=0 | Jumlah terjual |
| created_at | TIMESTAMP | NULL | Waktu dibuat |
| updated_at | TIMESTAMP | NULL | Waktu terakhir update |

**Indexes**:
- `category_id`: Untuk query by kategori
- `sold_quantity` (DESC): Untuk ranking menu populer

**Foreign Keys**:
```sql
FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
-- Jika kategori dihapus, semua menu di kategori itu juga dihapus
```

**Data Contoh**:
```sql
INSERT INTO menus VALUES
(1, 1, 'Cheese Burger', 'Burger dengan keju mozzarella', 35000.00, '/storage/menus/burger1.jpg', 15, '2025-11-27 10:00:00', '2025-11-27 10:00:00'),
(2, 1, 'Beef Burger', 'Burger dengan daging sapi premium', 45000.00, '/storage/menus/burger2.jpg', 12, '2025-11-27 10:00:00', '2025-11-27 10:00:00'),
(3, 2, 'Margarita Pizza', 'Pizza klasik dengan tomat & keju', 65000.00, '/storage/menus/pizza1.jpg', 8, '2025-11-27 10:00:00', '2025-11-27 10:00:00'),
(4, 3, 'Iced Tea', 'Minuman segar teh dingin', 12000.00, '/storage/menus/drink1.jpg', 25, '2025-11-27 10:00:00', '2025-11-27 10:00:00');
```

---

### 4. ORDERS Table

**Tujuan**: Menyimpan header penjualan (ringkasan order)

**Fields**:

| Field | Type | Constraint | Keterangan |
|-------|------|-----------|-----------|
| id | BIGINT | PK, AUTO_INCREMENT | Primary key |
| order_number | VARCHAR(255) | NOT NULL, UNIQUE | Nomor order unik |
| total_amount | DECIMAL(12,2) | NOT NULL | Total harga (setelah diskon) |
| total_quantity | INT | UNSIGNED, NOT NULL | Jumlah total items |
| status | ENUM | NOT NULL, DEFAULT='pending' | Status order (pending/completed/cancelled) |
| payment_method | ENUM | NOT NULL | Metode pembayaran |
| completed_at | TIMESTAMP | NULL | Waktu order selesai |
| created_at | TIMESTAMP | NULL | Waktu order dibuat |
| updated_at | TIMESTAMP | NULL | Waktu terakhir update |

**Enum Values**:

```sql
-- Status
completed  : Order sudah selesai (pembayaran sukses)
pending    : Order belum selesai/pembayaran
cancelled  : Order dibatalkan

-- Payment Method
cash       : Pembayaran tunai
credit_card: Pembayaran kartu kredit
qris       : Pembayaran QRIS/e-wallet
```

**Indexes**:
- `order_number` (UNIQUE): Lookup by order number
- `created_at`: Untuk filter tanggal (dashboard)
- `status`: Untuk filter order status
- `payment_method`: Untuk aggregate by payment

**Data Contoh**:
```sql
INSERT INTO orders VALUES
(1, 'ORD-1732730400', 110000.00, 3, 'completed', 'cash', '2025-11-27 15:30:00', '2025-11-27 15:30:00', '2025-11-27 15:30:00'),
(2, 'ORD-1732730500', 200000.00, 5, 'completed', 'credit_card', '2025-11-27 15:35:00', '2025-11-27 15:35:00', '2025-11-27 15:35:00');
```

---

### 5. ORDER_ITEMS Table

**Tujuan**: Menyimpan detail items yang ada di setiap order (junction table)

**Fields**:

| Field | Type | Constraint | Keterangan |
|-------|------|-----------|-----------|
| id | BIGINT | PK, AUTO_INCREMENT | Primary key |
| order_id | BIGINT | FK, NOT NULL | Foreign key ke orders |
| menu_id | BIGINT | FK, NOT NULL | Foreign key ke menus |
| quantity | INT | UNSIGNED, NOT NULL | Jumlah item dipesan |
| price | DECIMAL(10,2) | NOT NULL | Harga per unit saat order |
| discount | DECIMAL(10,2) | DEFAULT=0 | Diskon per item (dalam rupiah) |
| created_at | TIMESTAMP | NULL | Waktu dibuat |
| updated_at | TIMESTAMP | NULL | Waktu terakhir update |

**Foreign Keys**:
```sql
FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
-- Jika order dihapus, items juga terhapus

FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE RESTRICT
-- Tidak boleh hapus menu jika masih ada order items
```

**Indexes**:
- `order_id`: Untuk query items by order
- `menu_id`: Untuk query orders by menu

**Penjelasan Field Price & Discount**:
```
Saat order dibuat:
- price: Simpan harga saat itu (historical data)
- Jika harga menu berubah nanti, order item tetap catat harga lama

Contoh:
Menu "Burger" harga 35000, tapi saat order dibuat harganya 35000 → simpan 35000
Jika nanti menu "Burger" harganya dinaikkan jadi 40000, 
Order items yang lama tetap catat 35000 (untuk correct calculation)
```

**Data Contoh**:
```sql
-- Order #1 (total 110000) punya 3 items:
INSERT INTO order_items VALUES
(1, 1, 1, 1, 35000.00, 0.00, '2025-11-27 15:30:00', '2025-11-27 15:30:00'),
-- 1 Cheese Burger @ 35000
(2, 1, 3, 2, 65000.00, 10000.00, '2025-11-27 15:30:00', '2025-11-27 15:30:00'),
-- 2 Pizza @ 65000 - diskon 10000
(3, 1, 4, 1, 12000.00, 0.00, '2025-11-27 15:30:00', '2025-11-27 15:30:00');
-- 1 Iced Tea @ 12000

-- Total = (1*35000-0) + (2*65000-10000) + (1*12000-0) 
--       = 35000 + 120000 + 12000 
--       = 167000
```

---

### 6. TRANSACTIONS Table

**Tujuan**: Menyimpan detail pembayaran untuk setiap order

**Fields**:

| Field | Type | Constraint | Keterangan |
|-------|------|-----------|-----------|
| id | BIGINT | PK, AUTO_INCREMENT | Primary key |
| order_id | BIGINT | FK, NOT NULL, UNIQUE | Foreign key ke orders (1:1) |
| payment_method | ENUM | NOT NULL | Metode pembayaran |
| amount_paid | DECIMAL(12,2) | NOT NULL | Jumlah uang yang diterima |
| change_amount | DECIMAL(12,2) | NOT NULL | Kembalian uang |
| status | ENUM | NOT NULL, DEFAULT='pending' | Status pembayaran |
| paid_at | TIMESTAMP | NULL | Waktu pembayaran selesai |
| created_at | TIMESTAMP | NULL | Waktu dibuat |
| updated_at | TIMESTAMP | NULL | Waktu terakhir update |

**Enum Values**:
```sql
-- Status
pending  : Menunggu pembayaran
success  : Pembayaran berhasil
failed   : Pembayaran gagal
```

**Foreign Keys**:
```sql
FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE UNIQUE
-- Setiap order hanya punya satu transaction (1:1 relationship)
-- UNIQUE memastikan hanya satu transaction per order
```

**Indexes**:
- `order_id`: Lookup transaction by order
- `status`: Filter by transaction status
- `paid_at`: Query pembayaran by tanggal

**Perhitungan Change Amount**:
```
change_amount = amount_paid - order.total_amount

Contoh:
Order total: 110000
Customer bayar: 150000
Kembalian: 150000 - 110000 = 40000
```

**Data Contoh**:
```sql
INSERT INTO transactions VALUES
(1, 1, 'cash', 150000.00, 40000.00, 'success', '2025-11-27 15:30:00', '2025-11-27 15:30:00', '2025-11-27 15:30:00'),
-- Order 1 (110000), dibayar 150000 tunai, kembalian 40000

(2, 2, 'credit_card', 200000.00, 0.00, 'success', '2025-11-27 15:35:00', '2025-11-27 15:35:00', '2025-11-27 15:35:00');
-- Order 2 (200000), dibayar 200000 kartu kredit, tidak ada kembalian
```

---

## 🔗 Relationship Overview

### One-to-Many (1:N)

#### CATEGORIES → MENUS
```
1 kategori bisa punya banyak menu

Contoh:
Kategori "Burger" punya:
  - Cheese Burger
  - Beef Burger
  - Chicken Burger

Code di Category model:
public function menus() {
    return $this->hasMany(Menu::class);
}

Code di Menu model:
public function category() {
    return $this->belongsTo(Category::class);
}
```

#### MENUS → ORDER_ITEMS
```
1 menu bisa ada di banyak order

Contoh:
Menu "Burger" bisa dipesan di:
  - Order #1 (quantity 1)
  - Order #2 (quantity 2)
  - Order #3 (quantity 1)

Code di Menu model:
public function orderItems() {
    return $this->hasMany(OrderItem::class);
}

Code di OrderItem model:
public function menu() {
    return $this->belongsTo(Menu::class);
}
```

#### ORDERS → ORDER_ITEMS
```
1 order punya banyak items

Contoh:
Order #1 punya:
  - 1x Burger (35000)
  - 2x Pizza (130000)
  - 1x Minuman (12000)

Code di Order model:
public function items() {
    return $this->hasMany(OrderItem::class);
}

Code di OrderItem model:
public function order() {
    return $this->belongsTo(Order::class);
}
```

---

### One-to-One (1:1)

#### ORDERS → TRANSACTIONS
```
1 order punya 1 transaction pembayaran

Contoh:
Order #1 (110000) memiliki:
  Transaction #1 (dibayar 150000, kembalian 40000)

Code di Order model:
public function transaction() {
    return $this->hasOne(Transaction::class);
}

Code di Transaction model:
public function order() {
    return $this->belongsTo(Order::class);
}
```

---

## 💾 SQL Relationships

### Join Queries

```sql
-- Get order dengan semua itemnya
SELECT o.id, o.order_number, oi.*, m.name
FROM orders o
JOIN order_items oi ON o.id = oi.order_id
JOIN menus m ON oi.menu_id = m.id
WHERE o.id = 1;

-- Get menu dengan kategorinya
SELECT m.id, m.name, c.name as category_name
FROM menus m
JOIN categories c ON m.category_id = c.id;

-- Get transaction dengan order details
SELECT t.*, o.order_number, o.total_amount
FROM transactions t
JOIN orders o ON t.order_id = o.id
WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY);
```

---

## 📈 Query Examples

### Analytics Queries

```sql
-- Total revenue 30 hari terakhir
SELECT SUM(total_amount) as total_revenue
FROM orders
WHERE status = 'completed'
AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY);

-- Payment method distribution
SELECT payment_method, COUNT(*) as count, SUM(total_amount) as total
FROM orders
WHERE status = 'completed'
AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY payment_method;

-- Popular menus
SELECT m.id, m.name, m.sold_quantity
FROM menus m
ORDER BY m.sold_quantity DESC
LIMIT 5;

-- Daily sales chart
SELECT DATE(created_at) as date, 
       SUM(total_amount) as total,
       COUNT(*) as count
FROM orders
WHERE status = 'completed'
AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY DATE(created_at)
ORDER BY date;
```

---

## 🔐 Constraints & Triggers

### Primary Keys
```sql
-- Setiap table punya primary key untuk unique identifier
PRIMARY KEY (id)
```

### Foreign Keys
```sql
-- CATEGORIES → MENUS
FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE

-- ORDERS → ORDER_ITEMS
FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE

-- MENUS ← ORDER_ITEMS
FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE RESTRICT

-- ORDERS → TRANSACTIONS
FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
```

**Penjelasan Cascade Options**:
- **CASCADE**: Jika parent dihapus, child juga dihapus
- **RESTRICT**: Tidak boleh hapus parent jika masih ada child
- **SET NULL**: Jika parent dihapus, FK child jadi NULL

---

## 📊 Cardinality Rules

```
User : Order          = 1:N   (1 user bisa punya banyak order)
Category : Menu       = 1:N   (1 kategori punya banyak menu)
Menu : OrderItem      = 1:N   (1 menu di banyak order)
Order : OrderItem     = 1:N   (1 order punya banyak items)
Order : Transaction   = 1:1   (1 order = 1 transaksi)
```

---

**Selesai! ERD documentation lengkap. 📊✅**

