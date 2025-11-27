# 📚 DOKUMENTASI LENGKAP POS-KASIR

**Bahasa**: Bahasa Indonesia  
**Versi**: 1.0.0  
**Framework**: Laravel 11  
**Database**: MySQL 5.7+  
**PHP**: 8.2+

---

## 📋 DAFTAR ISI

1. [Startup & Git Clone](#startup--git-clone)
2. [Struktur Folder Proyek](#struktur-folder-proyek)
3. [Alur Coding & Arsitektur](#alur-coding--arsitektur)
4. [Entity Relationship Diagram (ERD)](#entity-relationship-diagram-erd)
5. [Penjelasan untuk Ujian Kompetensi](#penjelasan-untuk-ujian-kompetensi)

---

## 🚀 Startup & Git Clone

### A. Langkah-Langkah Awal (Setelah Git Clone)

#### 1. Persiapan Lingkungan
```bash
# Navigasi ke folder proyek
cd c:\xampp\htdocs\POS-Kasir

# Pastikan folder vendor dan node_modules tidak ada
# Jika ada, hapus agar bisa install fresh
Remove-Item -Recurse -Force vendor -ErrorAction SilentlyContinue
Remove-Item -Recurse -Force node_modules -ErrorAction SilentlyContinue
```

#### 2. Install Dependencies PHP (Composer)
```bash
composer install
```

**Penjelasan**: 
- Composer membaca file `composer.json` dan mengunduh semua library PHP yang dibutuhkan
- Hasil disimpan di folder `vendor/`
- Proses ini mirip seperti `npm install` untuk Node.js

#### 3. Setup Environment File
```bash
# Copy file .env.example menjadi .env
copy .env.example .env

# Generate application key (untuk enkripsi session & data)
php artisan key:generate
```

**Penjelasan**: 
- File `.env` adalah konfigurasi aplikasi yang tidak boleh disimpan di Git (untuk keamanan)
- `APP_KEY` digunakan untuk enkripsi data sensitif
- Setiap developer bisa punya `.env` berbeda sesuai lingkungan lokal mereka

#### 4. Konfigurasi Database (Edit `.env`)
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos-kasir
DB_USERNAME=root
DB_PASSWORD=
```

**Penjelasan**:
- Sesuaikan dengan konfigurasi MySQL di localhost Anda
- Biasanya XAMPP sudah bawaan MySQL dengan user `root` tanpa password

#### 5. Buat Database
```bash
# Menggunakan MySQL command line
mysql -u root -e "CREATE DATABASE pos-kasir;"

# ATAU gunakan artisan command (jika DB sudah ada)
php artisan migrate:fresh --seed
```

**Penjelasan**:
- Database dibuat dengan nama `pos-kasir`
- `--seed` akan menjalankan seeders untuk mengisi data dummy (user, kategori, menu, order, dll)

#### 6. Install Dependencies Frontend (Node.js)
```bash
# Jika ingin menggunakan asset bundler Vite
npm install

# Build asset CSS & JS
npm run build

# Atau untuk development
npm run dev
```

**Penjelasan**:
- Mengunduh semua library JavaScript/CSS dari `package.json`
- Vite adalah bundler modern pengganti Webpack untuk Laravel
- `npm run dev` menjalankan development server dengan hot reload

#### 7. Generate Storage Link
```bash
php artisan storage:link
```

**Penjelasan**:
- Membuat symbolic link dari `storage/app/public` ke `public/storage`
- Agar file yang diupload bisa diakses melalui browser

#### 8. Jalankan Development Server
```bash
php artisan serve
```

**Output**:
```
Starting Laravel development server: http://127.0.0.1:8000
```

#### 9. Akses Aplikasi
```
Browser: http://localhost:8000
```

**Akun Demo**:
- Email: `demo@example.com` | Password: `password` (Role: Admin)
- Email: `cashier@example.com` | Password: `cashier` (Role: Cashier)

---

### B. Startup Cepat (Menggunakan Script)

Jika ada file `setup.sh` atau `setup.bat`, jalankan:

**Windows (PowerShell)**:
```powershell
composer install; `
copy .env.example .env; `
php artisan key:generate; `
php artisan migrate:fresh --seed; `
php artisan storage:link; `
npm install; `
npm run build
```

---

## 📁 Struktur Folder Proyek

```
POS-Kasir/
│
├── 📂 app/
│   ├── 📂 Http/
│   │   ├── 📂 Controllers/
│   │   │   ├── AuthController.php           # Login, Register, Logout
│   │   │   ├── DashboardController.php      # Analytics & Statistik
│   │   │   ├── MenuController.php           # Menu CRUD
│   │   │   ├── OrderController.php          # Order CRUD
│   │   │   ├── CategoryController.php       # Kategori Menu CRUD
│   │   │   ├── CashierController.php        # POS Kasir (Add to Cart, Checkout)
│   │   │   └── Controller.php               # Base Controller
│   │   │
│   │   └── 📂 Middleware/
│   │       ├── Authenticate.php             # Check login
│   │       └── Cashier.php                  # Check role cashier
│   │
│   ├── 📂 Models/
│   │   ├── User.php                         # Model User (Login)
│   │   ├── Category.php                     # Model Kategori Menu
│   │   ├── Menu.php                         # Model Menu Item
│   │   ├── Order.php                        # Model Order Header
│   │   ├── OrderItem.php                    # Model Order Items (Detail)
│   │   └── Transaction.php                  # Model Transaksi/Pembayaran
│   │
│   └── 📂 Providers/
│       └── AppServiceProvider.php           # Service Provider Konfigurasi
│
├── 📂 database/
│   ├── 📂 migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2024_01_01_000002_create_categories_table.php
│   │   ├── 2024_01_01_000003_create_menus_table.php
│   │   ├── 2024_01_01_000004_create_orders_table.php
│   │   ├── 2024_01_01_000005_create_order_items_table.php
│   │   ├── 2024_01_01_000006_create_transactions_table.php
│   │   └── 2025_11_27_add_role_to_users.php
│   │
│   ├── 📂 factories/
│   │   └── UserFactory.php                  # Factory untuk testing
│   │
│   └── 📂 seeders/
│       └── DatabaseSeeder.php               # Data dummy untuk development
│
├── 📂 resources/
│   ├── 📂 views/
│   │   ├── 📂 layouts/
│   │   │   ├── app.blade.php                # Layout utama (navbar, sidebar)
│   │   │   └── auth.blade.php               # Layout untuk login/register
│   │   │
│   │   ├── 📂 auth/
│   │   │   ├── login.blade.php              # Halaman login
│   │   │   └── register.blade.php           # Halaman register
│   │   │
│   │   ├── 📂 dashboard/
│   │   │   └── index.blade.php              # Dashboard dengan statistik
│   │   │
│   │   ├── 📂 categories/
│   │   │   ├── index.blade.php              # List kategori
│   │   │   ├── create.blade.php             # Form tambah kategori
│   │   │   └── edit.blade.php               # Form edit kategori
│   │   │
│   │   ├── 📂 menus/
│   │   │   ├── index.blade.php              # List menu
│   │   │   ├── create.blade.php             # Form tambah menu
│   │   │   └── edit.blade.php               # Form edit menu
│   │   │
│   │   ├── 📂 orders/
│   │   │   ├── index.blade.php              # List order
│   │   │   ├── create.blade.php             # Form buat order baru
│   │   │   └── edit.blade.php               # Form edit order
│   │   │
│   │   ├── 📂 cashier/
│   │   │   └── index.blade.php              # Halaman POS Kasir (cart, checkout)
│   │   │
│   │   └── welcome.blade.php                # Landing page
│   │
│   ├── 📂 css/
│   │   └── app.css                          # Custom CSS
│   │
│   └── 📂 js/
│       ├── app.js                           # Entry point JavaScript
│       └── bootstrap.js                     # Bootstrap konfigurasi
│
├── 📂 routes/
│   ├── web.php                              # Semua route web aplikasi
│   └── console.php                          # Command Artisan yang custom
│
├── 📂 config/
│   ├── app.php                              # Konfigurasi aplikasi
│   ├── auth.php                             # Konfigurasi authentikasi
│   ├── database.php                         # Konfigurasi database
│   ├── cache.php                            # Konfigurasi caching
│   ├── filesystems.php                      # Konfigurasi file storage
│   ├── mail.php                             # Konfigurasi email
│   ├── queue.php                            # Konfigurasi queue job
│   ├── session.php                          # Konfigurasi session
│   └── services.php                         # Konfigurasi external services
│
├── 📂 storage/
│   ├── 📂 app/
│   │   └── 📂 public/
│   │       └── 📂 menus/                    # File upload menu images
│   │
│   ├── 📂 framework/
│   │   ├── 📂 cache/                        # Cache files
│   │   ├── 📂 sessions/                     # Session files
│   │   ├── 📂 testing/                      # Test files
│   │   └── 📂 views/                        # Compiled views
│   │
│   └── 📂 logs/
│       └── laravel.log                      # Application logs
│
├── 📂 tests/
│   ├── 📂 Feature/
│   │   └── ExampleTest.php                  # Feature tests
│   │
│   └── 📂 Unit/
│       └── ExampleTest.php                  # Unit tests
│
├── 📂 bootstrap/
│   ├── app.php                              # Bootstrap aplikasi
│   └── providers.php                        # Service providers
│
├── 📂 vendor/                               # (JANGAN DIEDIT) Semua library dari Composer
│
├── .env.example                             # Template .env
├── .gitignore                               # File yang diabaikan Git
├── artisan                                  # CLI Laravel
├── composer.json                            # Daftar dependencies PHP
├── composer.lock                            # Lock file composer
├── package.json                             # Daftar dependencies Node.js
├── package-lock.json                        # Lock file npm
├── phpunit.xml                              # Konfigurasi PHPUnit testing
├── vite.config.js                           # Konfigurasi Vite bundler
└── README.md                                # Dokumentasi default Laravel
```

---

## 🔄 Alur Coding & Arsitektur

### A. Architecture Pattern: MVC (Model-View-Controller)

```
┌─────────────────────────────────────────────────────┐
│                    USER (Browser)                    │
└──────────────────────┬──────────────────────────────┘
                       │
                       ↓
        ┌──────────────────────────────┐
        │      VIEW (Blade Template)    │
        │  - resources/views/*.blade.php│
        │  - Menampilkan UI ke user     │
        └──────────────┬───────────────┘
                       │
                       ↓
        ┌──────────────────────────────┐
        │   CONTROLLER (Request Handler)│
        │  - app/Http/Controllers/*.php │
        │  - Logika request-response     │
        └──────────────┬───────────────┘
                       │
                       ↓
        ┌──────────────────────────────┐
        │   MODEL (Database Access)     │
        │  - app/Models/*.php           │
        │  - Query builder Eloquent ORM │
        └──────────────┬───────────────┘
                       │
                       ↓
              ┌────────────────┐
              │   DATABASE     │
              │   (MySQL)      │
              └────────────────┘
```

### B. Request Flow (Alur Perjalanan Request)

#### Contoh: User Menambahkan Menu Baru

```
1. USER ACTION
   ├─ User klik tombol "Tambah Menu"
   └─ Form create menu terbuka

2. SUBMIT FORM
   ├─ User isi form (nama, harga, kategori, dll)
   ├─ User klik "Simpan"
   └─ Request POST dikirim ke server

3. ROUTER (routes/web.php)
   ├─ Route::post('/menus', [MenuController::class, 'store'])
   └─ Arahkan request ke MenuController@store

4. CONTROLLER (MenuController.php)
   ├─ Validate input data
   │  └─ Jika ada error, redirect kembali dengan pesan error
   ├─ Handle file upload (jika ada)
   │  └─ Simpan image ke storage/app/public/menus/
   └─ Panggil Menu::create($data)

5. MODEL (Menu.php - Eloquent ORM)
   ├─ Set fillable properties
   ├─ Execute SQL INSERT
   └─ Return created record

6. DATABASE (MySQL)
   ├─ Insert data baru ke tabel "menus"
   ├─ Generate auto-increment ID
   └─ Return success/error

7. CONTROLLER (kembali)
   ├─ Cek apakah berhasil
   ├─ Redirect ke menus.index
   └─ Attach flash message "Menu berhasil ditambahkan"

8. VIEW (Redirect)
   ├─ Route::get('/menus', [MenuController::class, 'index'])
   ├─ Render menus/index.blade.php
   └─ Tampilkan semua menu dengan pesan sukses
```

### C. Alur CRUD Menu Lengkap

#### CREATE (Tambah Menu)
```
GET  /menus/create     → MenuController@create    → Form kosong
POST /menus            → MenuController@store     → Simpan + Redirect
```

#### READ (Lihat Menu)
```
GET  /menus            → MenuController@index     → Tampilkan list
GET  /menus/{id}/edit  → MenuController@edit      → Tampilkan detail
```

#### UPDATE (Edit Menu)
```
PUT  /menus/{id}       → MenuController@update    → Update + Redirect
```

#### DELETE (Hapus Menu)
```
DELETE /menus/{id}     → MenuController@destroy   → Hapus + Redirect
```

### D. Relationship Model (Hubungan Antar Model)

```
┌──────────────────────────────────────────────────────────┐
│                    RELATIONSHIPS                          │
└──────────────────────────────────────────────────────────┘

User (1) ─────────────────── (Many) Order
         └─ User bisa punya banyak order

Category (1) ────────────────── (Many) Menu
            └─ Kategori punya banyak menu

Menu (1) ─────────────────── (Many) OrderItem
      └─ Menu bisa ada di banyak order

Order (1) ──────────────────── (Many) OrderItem
       └─ Order punya banyak items

Order (1) ─────────────────────── (1) Transaction
       └─ Setiap order punya satu transaksi
```

### E. Data Flow: Membuat Order Baru

```
┌─────────────────────────────────────────────────────────────┐
│ STEP 1: User Akses Form Order                               │
└─────────────────────────────────────────────────────────────┘

GET /orders/create
  │
  └─→ OrderController@create
      └─→ $menus = Menu::all()
          └─→ view('orders.create', compact('menus'))

┌─────────────────────────────────────────────────────────────┐
│ STEP 2: User Submit Form dengan Items                       │
└─────────────────────────────────────────────────────────────┘

POST /orders
Data yang dikirim:
{
  "total_amount": 150000,
  "total_quantity": 3,
  "payment_method": "cash",
  "items": [
    {"menu_id": 1, "quantity": 1, "price": 35000, "discount": 0},
    {"menu_id": 2, "quantity": 2, "price": 45000, "discount": 5000},
  ]
}

┌─────────────────────────────────────────────────────────────┐
│ STEP 3: OrderController@store Proses Data                   │
└─────────────────────────────────────────────────────────────┘

1. Validasi input:
   - total_amount harus numeric
   - payment_method harus 'cash'|'credit_card'|'qris'
   - items minimal 1, dan setiap item valid

2. Buat Order Header:
   Order::create([
     'order_number' => 'ORD-' . time(),
     'total_amount' => 150000,
     'total_quantity' => 3,
     'payment_method' => 'cash',
     'status' => 'completed',
     'completed_at' => now()
   ])

3. Buat Order Items (Loop):
   foreach ($validated['items'] as $item) {
     OrderItem::create([
       'order_id' => $order->id,
       'menu_id' => $item['menu_id'],
       'quantity' => $item['quantity'],
       'price' => $item['price'],
       'discount' => $item['discount'] ?? 0
     ]);
     
     // Update sold_quantity di menu
     Menu::find($item['menu_id'])->increment('sold_quantity', $quantity);
   }

4. Redirect:
   return redirect()->route('orders.index')
          ->with('success', 'Order berhasil dibuat');

┌─────────────────────────────────────────────────────────────┐
│ STEP 4: View List Order                                      │
└─────────────────────────────────────────────────────────────┘

OrderController@index:
  $orders = Order::with('items')->paginate(10);
  return view('orders.index', compact('orders'));

View (orders/index.blade.php) menampilkan:
  - Nomor order, tanggal, total, status, payment method
  - Action buttons: View, Edit, Delete
```

---

## 🗂️ Entity Relationship Diagram (ERD)

### A. Struktur Database Visual

```
┌─────────────────────────────────────────────────────────────┐
│                   DATABASE: pos_kasir                        │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────┐
│      USERS              │
├─────────────────────────┤
│ id (PK)                 │
│ name                    │
│ email (UNIQUE)          │
│ password (hashed)       │
│ role (admin/cashier)    │
│ created_at              │
│ updated_at              │
└─────────────────────────┘

┌─────────────────────────┐
│     CATEGORIES          │
├─────────────────────────┤
│ id (PK)                 │
│ name                    │
│ slug                    │
│ description             │
│ created_at              │
│ updated_at              │
└─────────────────────────┘
         ▲
         │ 1:N
         │
┌────────┴──────────────────────────────┐
│           MENUS                        │
├──────────────────────────────────────┤
│ id (PK)                               │
│ category_id (FK → CATEGORIES.id)      │
│ name                                  │
│ description                           │
│ price (DECIMAL)                       │
│ image_url                             │
│ sold_quantity (untuk stats)           │
│ created_at                            │
│ updated_at                            │
└──────────────┬───────────────────────┘
               │
               │ 1:N
               ▼
┌───────────────────────────────────────────────┐
│          ORDER_ITEMS (Detail)                  │
├───────────────────────────────────────────────┤
│ id (PK)                                       │
│ order_id (FK → ORDERS.id)                     │
│ menu_id (FK → MENUS.id)                       │
│ quantity                                      │
│ price (harga saat order dibuat)               │
│ discount (diskon per item)                    │
│ created_at                                    │
│ updated_at                                    │
└───────────────┬───────────────────────────────┘
                │
                │ N:1
                ▼
┌───────────────────────────────────────────────┐
│          ORDERS (Header)                       │
├───────────────────────────────────────────────┤
│ id (PK)                                       │
│ order_number (UNIQUE)                         │
│ total_amount (DECIMAL)                        │
│ total_quantity (INT)                          │
│ status (pending/completed/cancelled)          │
│ payment_method (cash/credit_card/qris)        │
│ completed_at (DATETIME)                       │
│ created_at                                    │
│ updated_at                                    │
└───────────────┬───────────────────────────────┘
                │
                │ 1:1
                ▼
┌───────────────────────────────────────────────┐
│        TRANSACTIONS                            │
├───────────────────────────────────────────────┤
│ id (PK)                                       │
│ order_id (FK → ORDERS.id, UNIQUE)             │
│ payment_method (cash/credit_card/qris)        │
│ amount_paid (DECIMAL)                         │
│ change_amount (DECIMAL)                       │
│ status (pending/success/failed)               │
│ paid_at (DATETIME)                            │
│ created_at                                    │
│ updated_at                                    │
└───────────────────────────────────────────────┘
```

### B. Penjelasan Relationship

| Relationship | Penjelasan | Contoh |
|---|---|---|
| **CATEGORIES (1:N) MENUS** | Satu kategori bisa punya banyak menu | Kategori "Burger" punya menu: Cheese Burger, Beef Burger, Chicken Burger |
| **MENUS (1:N) ORDER_ITEMS** | Satu menu bisa ada di banyak order | Menu "Cheese Burger" bisa dipesan di Order #1, #2, #3, dll |
| **ORDERS (1:N) ORDER_ITEMS** | Satu order punya banyak items | Order #5 punya 3 items: Burger, Pizza, Minuman |
| **ORDERS (1:1) TRANSACTIONS** | Satu order punya satu pembayaran | Order #5 punya transaksi pembayaran tunai Rp 150.000 |

### C. Contoh Query Dengan Relationship

```php
// Ambil semua order dengan itemnya
$orders = Order::with('items')->get();

// Ambil order dengan items dan menu details
$orders = Order::with(['items' => function($query) {
    $query->with('menu');
}])->get();

// Ambil kategori dengan semua menunya
$category = Category::with('menus')->find(1);

// Ambil menu dengan kategorinya
$menu = Menu::with('category')->find(1);

// Ambil transaction beserta ordernya
$transaction = Transaction::with('order')->find(1);
```

---

## 📝 Penjelasan untuk Ujian Kompetensi

Ketika mengerjakan ujian kompetensi berbasis project ini, pastikan Anda bisa menjelaskan:

### 1. KONSEP DASAR

#### A. Apa itu POS-Kasir?
**Jawaban:**
- POS-Kasir adalah sistem Point of Sale (Kasir) yang digunakan untuk mengelola penjualan di restoran/cafe
- Sistem ini mencatat setiap transaksi (order, pembayaran, diskon, dll)
- Admin bisa melihat statistik penjualan, menu populer, dan performa kasir

#### B. Framework & Teknologi yang Digunakan?
**Jawaban:**
- **Backend**: Laravel 11 (PHP framework)
- **Frontend**: Blade Templates + Tailwind CSS
- **Database**: MySQL
- **Chart**: Chart.js untuk visualisasi data
- **Deployment**: XAMPP (local development)

#### C. Pattern/Arsitektur?
**Jawaban:**
- **MVC (Model-View-Controller)**:
  - **Model**: Representasi data (User, Menu, Order, dll)
  - **View**: Tampilan UI (HTML/Blade template)
  - **Controller**: Logika request-response
- **ORM**: Eloquent ORM untuk query database

---

### 2. FITUR-FITUR UTAMA

Jelaskan fitur yang Anda implementasi:

#### A. Dashboard Analytics
```
Fitur apa?
  - Menampilkan statistik penjualan real-time
  - Grafik sales 30 hari terakhir
  - Pie chart pembayaran per metode
  - Tabel menu populer

Teknologi:
  - DashboardController menggunakan Carbon untuk date range
  - Query builder untuk aggregate data (SUM, COUNT, GROUP BY)
  - Chart.js untuk visualisasi

Kode kunci:
  $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
                        ->where('status', 'completed')
                        ->sum('total_amount');
```

#### B. Menu Management (CRUD)
```
Fitur apa?
  - Create: Tambah menu baru
  - Read: Lihat list menu
  - Update: Edit menu
  - Delete: Hapus menu

Teknologi:
  - MenuController untuk logic CRUD
  - Menu model dengan Eloquent ORM
  - File upload untuk image
  - Validation input

Kode kunci:
  # Create
  $validated = $request->validate([...]);
  Menu::create($validated);
  
  # Read
  $menus = Menu::with('category')->paginate(10);
  
  # Update
  $menu->update($validated);
  
  # Delete
  $menu->delete();
```

#### C. Order Management
```
Fitur apa?
  - Buat order dengan multiple items
  - Apply diskon per item
  - Payment method: Cash, Credit Card, QRIS
  - Tracking order status: Pending, Completed, Cancelled

Teknologi:
  - OrderController + OrderItemController
  - Order & OrderItem model (one-to-many relationship)
  - Transactional logic untuk consistency

Kode kunci:
  # Create order
  $order = Order::create($orderData);
  foreach($items as $item) {
    OrderItem::create($item);
    Menu::find($item['menu_id'])->increment('sold_quantity', $qty);
  }
```

#### D. Authentication & Authorization
```
Fitur apa?
  - User login/register
  - Role-based access: Admin vs Cashier
  - Session management

Teknologi:
  - Laravel built-in Auth
  - User model dengan role field
  - Middleware untuk protecting routes

Middleware:
  - 'auth': Cek user sudah login
  - 'cashier': Cek user adalah kasir
```

---

### 3. DATABASE DESIGN

Ketika ditanya tentang database:

#### A. Table Relationships
```
Jelaskan relationship:

CATEGORIES (1) ─────────── (N) MENUS
└─ Satu kategori bisa punya banyak menu

MENUS (1) ─────────── (N) ORDER_ITEMS  
└─ Satu menu bisa ada di banyak order

ORDERS (1) ─────────── (N) ORDER_ITEMS
└─ Satu order punya banyak items

ORDERS (1) ─────────── (1) TRANSACTIONS
└─ Satu order punya satu pembayaran
```

#### B. Normalization (3NF)
```
Jawaban:
- Database sudah dinormalisasi ke 3NF
- Tidak ada redundansi data
- Setiap table punya primary key unik
- Foreign key relationships correct

Contoh:
- MENUS tidak menyimpan category_name, hanya category_id (FK)
- ORDER_ITEMS menyimpan price saat order dibuat (historical data)
- Tidak ada derived fields yang perlu diupdate
```

---

### 4. IMPLEMENTASI CODING

Siapkan penjelasan untuk:

#### A. Validation (Input Validation)
```
Contoh di MenuController@store:

$validated = $request->validate([
  'name' => 'required|string|max:255',
  'price' => 'required|numeric|min:0',
  'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
  'category_id' => 'required|exists:categories,id',
]);

Penjelasan:
- required: field wajib diisi
- numeric: harus angka
- min:0: nilai minimal 0
- exists:categories,id: category_id harus ada di tabel categories
- image: file harus gambar
- mimes: format file harus jpeg/png
- max:2048: ukuran max 2MB
```

#### B. File Upload
```
Contoh di MenuController@store:

if ($request->hasFile('image')) {
  $path = $request->file('image')->store('menus', 'public');
  $validated['image_url'] = Storage::url($path);
}

Penjelasan:
- store('menus', 'public'): Simpan ke storage/app/public/menus/
- Storage::url(): Generate URL yang bisa diakses di browser
- File disimpan dengan nama random untuk security
```

#### C. Relationship Query (Eager Loading)
```
Contoh:

// WRONG - N+1 Query Problem
$orders = Order::all();
foreach($orders as $order) {
  echo $order->items->count(); // Query lagi untuk setiap order
}

// CORRECT - Eager Loading
$orders = Order::with('items')->get(); // 1 query + 1 query untuk items

Penjelasan:
- with('items'): Load relationship items sekaligus
- Mengurangi jumlah query ke database
- Performance lebih baik
```

#### D. Query Aggregation
```
Contoh di DashboardController@index:

// Total revenue 30 hari terakhir
$totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed')
                      ->sum('total_amount');

// Group by payment method
$paymentMethods = Order::whereBetween('created_at', [$startDate, $endDate])
                        ->groupBy('payment_method')
                        ->selectRaw('payment_method, COUNT(*) as count')
                        ->get();

Penjelasan:
- whereBetween: Filter by date range
- where: Additional filter (status completed)
- sum/count: Aggregate functions
- groupBy: Group results
- selectRaw: Raw SQL selection
```

---

### 5. TESTING & DEBUGGING

Siapkan jawaban untuk:

#### A. Bagaimana Menjalankan Testing?
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/MenuTest.php

# Run with detailed output
php artisan test --verbose
```

#### B. Debugging Tools
```bash
# Menggunakan Log
Log::info('Debug message', ['data' => $variable]);

# Check database state
php artisan tinker
>>> App\Models\Menu::count()
>>> App\Models\Order::with('items')->first()

# Check routes
php artisan route:list

# Check migrations
php artisan migrate:status
```

---

### 6. SECURITY FEATURES

Jelaskan security yang diimplementasi:

#### A. CSRF Protection
```
Setiap form harus punya:
<form method="POST" action="/menus">
  @csrf
  ...
</form>

Penjelasan:
- CSRF token mencegah cross-site request forgery
- Token di-verify di backend sebelum proses
```

#### B. Mass Assignment Protection
```
Di model:

protected $fillable = [
  'name',
  'price',
  'category_id',
  // TIDAK termasuk 'id', 'created_at', dll
];

Penjelasan:
- Hanya field di fillable yang bisa di-assign
- Mencegah attacker mengisi field yang tidak diinginkan
```

#### C. SQL Injection Prevention
```
WRONG:
$menus = DB::select("SELECT * FROM menus WHERE id = " . $id);

CORRECT:
$menu = Menu::find($id); // Menggunakan Eloquent ORM

Penjelasan:
- Eloquent ORM menggunakan parameterized queries
- Menghindari SQL injection attacks
```

#### D. Password Hashing
```
Di User model:

protected function casts(): array {
  return [
    'password' => 'hashed',
  ];
}

Penjelasan:
- Password di-hash menggunakan bcrypt/argon2
- Hash tidak bisa di-reverse
- User password terenkripsi di database
```

---

### 7. PERFORMANCE OPTIMIZATION

Jelaskan optimasi yang dilakukan:

#### A. Pagination
```
Di Controller:
$menus = Menu::with('category')->paginate(10);

Keuntungan:
- Hanya load 10 data per page
- Mengurangi memory usage
- UI lebih responsif
```

#### B. Eager Loading
```
$orders = Order::with(['items', 'transaction'])->paginate(10);

Keuntungan:
- Mengurangi N+1 query problem
- Lebih cepat ketika relasi diakses
```

#### C. Database Indexing
```
Di migrations:
$table->index('category_id');
$table->index('payment_method');
$table->index(['created_at', 'status']);

Keuntungan:
- Query lebih cepat
- WHERE clause lebih efisien
- Aggregate query (GROUP BY) lebih fast
```

---

### 8. PERTANYAAN YANG MUNGKIN DITANYA

#### A. "Jelaskan Flow ketika user membuat order baru"
```
Jawaban:
1. User akses /orders/create
   → OrderController@create load form dengan daftar menu

2. User isi form dan submit
   → POST request ke /orders dengan data items

3. OrderController@store:
   a. Validate input (total_amount, items, payment_method)
   b. Insert ke tabel ORDERS (create order header)
   c. Loop setiap item:
      - Insert ke tabel ORDER_ITEMS
      - Update sold_quantity di MENUS
   d. Redirect ke orders.index dengan success message

4. View:
   → Tampilkan list semua order dengan order yang baru dibuat
```

#### B. "Bagaimana Relationship User → Order?"
```
Jawaban:
Sebenarnya di project ini belum di-implement, tapi harusnya:

Di User model:
public function orders() {
  return $this->hasMany(Order::class);
}

Di Order migration:
$table->foreignId('user_id')->constrained('users');

Alasan:
- Setiap order dibuat oleh satu user
- User bisa membuat banyak order
- Bisa tracking siapa yang membuat order
```

#### C. "Bagaimana Menghitung Total Order dengan Diskon?"
```
Jawaban:
Di ORDER_ITEMS table:
- quantity: Jumlah item
- price: Harga satuan
- discount: Diskon per item

Total per item = (quantity * price) - discount
Total order = SUM(quantity * price - discount)

Di Controller/View:
@foreach($order->items as $item)
  $subtotal = ($item->quantity * $item->price) - $item->discount
@endforeach
$total = $order->total_amount
```

#### D. "Apa Perbedaan ORDERS dan TRANSACTIONS table?"
```
Jawaban:

ORDERS:
- Order header (ringkasan penjualan)
- Field: order_number, total_amount, status, payment_method

TRANSACTIONS:
- Detail pembayaran
- Field: amount_paid, change_amount, status

Contoh:
Order #1:
- total_amount: 150.000
- payment_method: cash

Transaction #1:
- amount_paid: 200.000 (uang yang diserahkan pembeli)
- change_amount: 50.000 (kembalian)
- status: success
```

---

### 9. FITUR-FITUR YANG BISA DITAMBAHKAN

Siapkan ide improvement untuk ujian:

```
1. Reporting/Export
   - Export order ke Excel/PDF
   - Print nota/receipt

2. Inventory Management
   - Tracking stok menu
   - Alert when stock low

3. User Management
   - List user, edit role, delete user
   - Activity log per user

4. Payment Integration
   - Integration dengan Midtrans/GCash
   - Automatic transaction verification

5. Multi-tenancy
   - Support multiple outlet
   - Per-outlet reporting

6. Mobile App
   - Native app untuk cashier
   - Offline mode
```

---

### 10. KESIMPULAN POIN-POIN PENTING

Saat ujian, pastikan Anda bisa menjelaskan:

✅ **Architecture**: MVC pattern + Eloquent ORM  
✅ **Request Flow**: Route → Controller → Model → Database  
✅ **Database Design**: Proper relationships & normalization  
✅ **Validation**: Input validation & error handling  
✅ **Security**: CSRF, mass assignment protection, password hashing  
✅ **Performance**: Eager loading, pagination, indexing  
✅ **Code Quality**: Clean code, proper naming, documentation  
✅ **Testing**: Unit & feature tests  
✅ **Deployment**: How to setup & run the project  

---

**Semoga dokumentasi ini membantu dalam persiapan ujian kompetensi! 💪**

