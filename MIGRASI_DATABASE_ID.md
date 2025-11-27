# 📊 DOKUMENTASI MIGRASI & SKEMA DATABASE

**Bahasa**: Bahasa Indonesia  
**File Location**: `database/migrations/`

---

## 📋 DAFTAR ISI

1. [Migration Overview](#migration-overview)
2. [Table Structures](#table-structures)
3. [Migration Details](#migration-details)
4. [Indexing Strategy](#indexing-strategy)

---

## 🔄 Migration Overview

### Apa itu Migration?

Migration adalah file PHP yang mendefinisikan struktur database. Setiap migration:
- Merepresentasikan satu perubahan database
- Bisa di-rollback (undo) kapan saja
- Di-version control (tersimpan di Git)
- Bersifat reversible (up & down)

### Migration File Naming Convention

```
YYYY_MM_DD_HHMMSS_deskripsi_dalam_snake_case.php

Contoh:
2024_01_01_000003_create_menus_table.php
2025_11_27_add_role_to_users.php
```

Format timestamp memastikan urutan eksekusi yang konsisten.

---

## 📑 Table Structures

### 1. USERS Table

**File**: `0001_01_01_000000_create_users_table.php`

```sql
CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  email_verified_at TIMESTAMP NULL,
  password VARCHAR(255) NOT NULL,
  remember_token VARCHAR(100) NULL,
  role ENUM('admin', 'cashier') DEFAULT 'cashier',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  INDEX idx_email (email),
  INDEX idx_role (role)
);
```

**Penjelasan Field**:
- `id`: Primary key, auto-increment
- `name`: Nama user (required)
- `email`: Email unik (untuk login)
- `password`: Password di-hash
- `role`: Admin atau Cashier (added di migration 2025_11_27)
- `created_at`, `updated_at`: Timestamp Laravel (auto)
- `remember_token`: Untuk "Remember Me" feature (optional)

**Enum Values**:
- `admin`: Bisa akses semua fitur (menu, order, dashboard, kategori)
- `cashier`: Hanya akses kasir (POS interface)

---

### 2. CATEGORIES Table

**File**: `2024_01_01_000002_create_categories_table.php`

```sql
CREATE TABLE categories (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  description TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  INDEX idx_slug (slug)
);
```

**Penjelasan Field**:
- `id`: Primary key
- `name`: Nama kategori (e.g., "Burger", "Pizza", "Minuman")
- `slug`: URL-friendly version (e.g., "burger", "pizza", "minuman")
  - Auto-generated dari nama menggunakan `Str::slug()`
  - Unique untuk prevent duplicates
- `description`: Deskripsi kategori (optional)

**Data Seed** (DatabaseSeeder.php):
```php
Category::create([
  'name' => 'Makanan',
  'slug' => 'makanan',
  'description' => 'Kategori makanan',
]);
```

---

### 3. MENUS Table

**File**: `2024_01_01_000003_create_menus_table.php`

```sql
CREATE TABLE menus (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(255) NOT NULL,
  description TEXT NULL,
  price DECIMAL(10, 2) NOT NULL,
  image_url VARCHAR(255) NULL,
  sold_quantity INT UNSIGNED DEFAULT 0,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
  INDEX idx_category_id (category_id),
  INDEX idx_name (name),
  INDEX idx_sold_quantity (sold_quantity)
);
```

**Penjelasan Field**:
- `id`: Primary key
- `category_id`: Foreign key ke `categories.id`
  - ON DELETE CASCADE: Jika kategori dihapus, menu terhapus juga
- `name`: Nama menu (e.g., "Cheese Burger")
- `description`: Deskripsi panjang menu
- `price`: Harga menu dalam DECIMAL(10,2)
  - 10 digits total, 2 decimal places
  - Contoh: 35000.50
- `image_url`: Path/URL gambar menu
- `sold_quantity`: Jumlah terjual (untuk ranking/stats)
  - Incremented saat order dibuat
- `created_at`, `updated_at`: Timestamp

**Indexing**:
- `category_id`: Sering diquery saat filter by kategori
- `sold_quantity`: Untuk sorting menu populer

---

### 4. ORDERS Table

**File**: `2024_01_01_000004_create_orders_table.php`

```sql
CREATE TABLE orders (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_number VARCHAR(255) NOT NULL UNIQUE,
  total_amount DECIMAL(12, 2) NOT NULL,
  total_quantity INT UNSIGNED NOT NULL,
  status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
  payment_method ENUM('cash', 'credit_card', 'qris') NOT NULL,
  completed_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  INDEX idx_order_number (order_number),
  INDEX idx_status (status),
  INDEX idx_payment_method (payment_method),
  INDEX idx_created_at (created_at)
);
```

**Penjelasan Field**:
- `id`: Primary key
- `order_number`: Nomor unik order (e.g., "ORD-1732691234")
  - Format: "ORD-" + timestamp
  - Unique untuk prevent duplicates
- `total_amount`: Total harga order (setelah diskon)
- `total_quantity`: Jumlah item dalam order
- `status`: 
  - `pending`: Order dibuat, belum dibayar
  - `completed`: Order sudah dibayar/selesai
  - `cancelled`: Order dibatalkan
- `payment_method`:
  - `cash`: Pembayaran tunai
  - `credit_card`: Kartu kredit
  - `qris`: QRIS/e-wallet
- `completed_at`: Waktu order selesai (optional)

**Indexing**:
- `created_at`: Sering filter by date range (dashboard 30 hari)
- `status`: Filter order berdasarkan status
- `payment_method`: Pie chart payment methods

---

### 5. ORDER_ITEMS Table

**File**: `2024_01_01_000005_create_order_items_table.php`

```sql
CREATE TABLE order_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NOT NULL,
  menu_id BIGINT UNSIGNED NOT NULL,
  quantity INT UNSIGNED NOT NULL,
  price DECIMAL(10, 2) NOT NULL,
  discount DECIMAL(10, 2) DEFAULT 0,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE RESTRICT,
  INDEX idx_order_id (order_id),
  INDEX idx_menu_id (menu_id)
);
```

**Penjelasan Field**:
- `id`: Primary key
- `order_id`: Foreign key ke `orders.id`
  - ON DELETE CASCADE: Jika order dihapus, items terhapus juga
- `menu_id`: Foreign key ke `menus.id`
  - ON DELETE RESTRICT: Tidak boleh hapus menu yang masih ada order item
- `quantity`: Jumlah item yang dipesan
- `price`: Harga per unit saat order dibuat
  - Di-store untuk historical data (jika harga menu berubah)
- `discount`: Diskon per item
  - Default 0
  - Bisa dalam bentuk rupiah atau persentase

**Contoh Data**:
```
Order #1 punya 2 items:
┌─────┬────────┬──────────┬─────────┬──────────┐
│ id  │ menu_id│ quantity │ price   │ discount │
├─────┼────────┼──────────┼─────────┼──────────┤
│ 1   │ 1      │ 1        │ 35000   │ 0        │ (Cheese Burger)
│ 2   │ 2      │ 2        │ 45000   │ 5000     │ (Pasta x2, diskon 5k)
└─────┴────────┴──────────┴─────────┴──────────┘

Total = (1 × 35000 - 0) + (2 × 45000 - 5000) = 35000 + 85000 = 120000
```

---

### 6. TRANSACTIONS Table

**File**: `2024_01_01_000006_create_transactions_table.php`

```sql
CREATE TABLE transactions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NOT NULL UNIQUE,
  payment_method ENUM('cash', 'credit_card', 'qris') NOT NULL,
  amount_paid DECIMAL(12, 2) NOT NULL,
  change_amount DECIMAL(12, 2) NOT NULL,
  status ENUM('pending', 'success', 'failed') DEFAULT 'pending',
  paid_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  INDEX idx_order_id (order_id),
  INDEX idx_status (status),
  INDEX idx_paid_at (paid_at)
);
```

**Penjelasan Field**:
- `id`: Primary key
- `order_id`: Foreign key ke `orders.id` (UNIQUE)
  - Setiap order hanya punya satu transaction
- `payment_method`: Metode pembayaran
- `amount_paid`: Jumlah uang yang diterima dari pembeli
- `change_amount`: Kembalian
  - change_amount = amount_paid - order.total_amount
- `status`:
  - `pending`: Belum dibayar
  - `success`: Pembayaran berhasil
  - `failed`: Pembayaran gagal
- `paid_at`: Waktu pembayaran selesai

**Contoh**:
```
Order #1:
- total_amount: 150.000

Transaction #1:
- amount_paid: 200.000 (pembeli serahkan)
- change_amount: 50.000 (kembalian ke pembeli)
- status: success
```

---

## 🛠️ Migration Details

### How Migrations Work

#### 1. Running Migrations

```bash
# Run semua migration yang belum pernah dijalankan
php artisan migrate

# Run specific migration file
php artisan migrate --path=database/migrations/2024_01_01_000003_create_menus_table.php

# Rollback last batch
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset

# Fresh: Rollback semua, kemudian run ulang
php artisan migrate:fresh

# Fresh + Seed
php artisan migrate:fresh --seed

# Check migration status
php artisan migrate:status
```

#### 2. Migration Structure

Setiap migration file memiliki:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Buat table baru
        Schema::create('menus', function (Blueprint $table) {
            $table->id(); // BIGINT AUTO_INCREMENT
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Undo: Hapus table
        Schema::dropIfExists('menus');
    }
};
```

**Penjelasan**:
- `up()`: Ketika migration dijalankan (create table)
- `down()`: Ketika migration di-rollback (drop table)
- Harus reversible (bisa up dan down)

#### 3. Adding Role to Users (Migration 2025_11_27)

**File**: `2025_11_27_add_role_to_users.php`

```php
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambah kolom role jika belum ada
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'cashier'])
                      ->default('cashier')
                      ->after('password');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
```

**Penjelasan**:
- `Schema::table()`: Modify existing table (tidak create baru)
- `after('password')`: Kolom ditambah setelah password
- `default('cashier')`: Default value untuk existing rows
- `hasColumn()`: Check apakah kolom sudah ada (prevent error)

---

## 🔍 Indexing Strategy

### Apa itu Index?

Index adalah struktur data yang mempercepat query database.

**Analogi**: 
- Seperti index di buku
- Tanpa index: harus baca halaman demi halaman (slow)
- Dengan index: langsung ke halaman yang dicari (fast)

### Index di Project Ini

#### 1. Primary Keys
```sql
PRIMARY KEY (id) -- Setiap table punya ini
```
Otomatis di-index untuk fast lookup by ID.

#### 2. Foreign Keys
```sql
INDEX idx_category_id (category_id)        -- Di MENUS
INDEX idx_order_id (order_id)              -- Di ORDER_ITEMS
INDEX idx_menu_id (menu_id)                -- Di ORDER_ITEMS
```
Di-index karena sering digunakan untuk JOIN atau filter.

#### 3. Search Fields
```sql
INDEX idx_email (email)                    -- Di USERS
INDEX idx_order_number (order_number)      -- Di ORDERS
INDEX idx_name (name)                      -- Di MENUS
INDEX idx_slug (slug)                      -- Di CATEGORIES
```
Di-index untuk WHERE clause atau exact match.

#### 4. Filter/Aggregate Fields
```sql
INDEX idx_status (status)                  -- Di ORDERS (filter)
INDEX idx_payment_method (payment_method)  -- Di ORDERS (filter, GROUP BY)
INDEX idx_role (role)                      -- Di USERS (filter)
INDEX idx_sold_quantity (sold_quantity)    -- Di MENUS (ORDER BY)
```
Di-index untuk WHERE clause, GROUP BY, atau ORDER BY.

#### 5. Date Range Queries
```sql
INDEX idx_created_at (created_at)          -- Di ORDERS
INDEX idx_paid_at (paid_at)                -- Di TRANSACTIONS
```
Di-index untuk BETWEEN query di dashboard (30 hari terakhir).

#### 6. Composite Index (Multi-column)
```sql
-- Jika ada query yang sering pakai dua kolom sekaligus:
INDEX idx_status_created_at (status, created_at)
-- Baik untuk query seperti:
-- WHERE status='completed' AND created_at BETWEEN ? AND ?
```

### Index Best Practices

✅ **Index columns yang sering di-filter** (WHERE clause)  
✅ **Index columns yang sering di-sort** (ORDER BY)  
✅ **Index columns yang sering di-join** (FOREIGN KEY)  
✅ **Index date columns** untuk range queries  
❌ **Jangan over-index** (setiap index butuh space & update time)  
❌ **Jangan index boolean fields** (hanya 2 values)  
❌ **Jangan index low-cardinality columns** (banyak duplikat value)

---

## 🔗 Relationships di Code

### Eloquent Model Relationships

#### 1. One-to-Many (Category → Menu)

**Di Category Model**:
```php
public function menus()
{
    return $this->hasMany(Menu::class);
}
```

**Di Menu Model**:
```php
public function category()
{
    return $this->belongsTo(Category::class);
}
```

**Usage**:
```php
// Ambil kategori dengan semua menunya
$category = Category::with('menus')->find(1);

// Ambil menu beserta kategorinya
$menu = Menu::with('category')->find(1);

// Query: kategori yang punya menu
$categories = Category::has('menus')->get();
```

#### 2. One-to-Many (Order → OrderItem)

**Di Order Model**:
```php
public function items()
{
    return $this->hasMany(OrderItem::class);
}
```

**Di OrderItem Model**:
```php
public function order()
{
    return $this->belongsTo(Order::class);
}

public function menu()
{
    return $this->belongsTo(Menu::class);
}
```

**Usage**:
```php
// Ambil order dengan itemnya
$order = Order::with('items')->find(1);

// Ambil dengan menu details juga (nested eager loading)
$order = Order::with(['items' => function($query) {
    $query->with('menu');
}])->find(1);
```

#### 3. One-to-One (Order → Transaction)

**Di Order Model**:
```php
public function transaction()
{
    return $this->hasOne(Transaction::class);
}
```

**Di Transaction Model**:
```php
public function order()
{
    return $this->belongsTo(Order::class);
}
```

**Usage**:
```php
// Ambil order dengan transactionnya
$order = Order::with('transaction')->find(1);

// Akses transaction langsung
echo $order->transaction->amount_paid;
```

---

## 📈 Query Examples

### Dashboard Analytics Queries

```php
// Total revenue 30 hari terakhir
$totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed')
                      ->sum('total_amount');
// SELECT SUM(total_amount) FROM orders 
// WHERE created_at BETWEEN ? AND ? AND status='completed'

// Payment methods distribution
$paymentMethods = Order::whereBetween('created_at', [$startDate, $endDate])
                        ->where('status', 'completed')
                        ->selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as total')
                        ->groupBy('payment_method')
                        ->get();
// SELECT payment_method, COUNT(*) as count, SUM(total_amount) as total
// FROM orders
// WHERE created_at BETWEEN ? AND ? AND status='completed'
// GROUP BY payment_method

// Popular menu items
$popularMenus = Menu::orderBy('sold_quantity', 'desc')
                     ->take(5)
                     ->get();
// SELECT * FROM menus ORDER BY sold_quantity DESC LIMIT 5
```

---

**Selesai! Semoga dokumentasi database ini membantu. 📚**

