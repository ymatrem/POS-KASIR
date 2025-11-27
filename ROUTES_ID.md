# 🛣️ DOKUMENTASI ROUTES & QUICK REFERENCE

**Bahasa**: Bahasa Indonesia  
**File Location**: `routes/web.php`

---

## 📋 DAFTAR ISI

1. [Route Overview](#route-overview)
2. [Route Groups](#route-groups)
3. [Complete Route List](#complete-route-list)
4. [Middleware Explanation](#middleware-explanation)
5. [Quick Reference Cheatsheet](#quick-reference-cheatsheet)

---

## 🗺️ Route Overview

### Apa itu Route?

Route adalah mapping antara URL/HTTP method dengan Controller method.

**Analogi**: 
- Route seperti "pintu" ke aplikasi
- Setiap pintu (route) membawa ke method yang berbeda
- HTTP method (GET, POST, PUT, DELETE) menentukan aksi apa

### Route File Structure

File `routes/web.php` adalah tempat semua web routes didefinisikan.

---

## 📦 Route Groups

### 1. Public Routes (No Authentication)

**Status**: ❌ Tidak perlu login  
**Routes**:
- GET `/login` → AuthController@showLogin
- POST `/login` → AuthController@authenticate
- GET `/register` → AuthController@showRegister
- POST `/register` → AuthController@register

**Middleware**: `guest` (jika sudah login, redirect ke dashboard)

**Kode**:
```php
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});
```

---

### 2. Authenticated Routes (Semua User)

**Status**: ✅ Perlu login  
**Routes**:
- Dashboard, Menu, Order, Category (CRUD)
- API endpoints untuk chart data

**Middleware**: `auth` (harus login)

**Kode**:
```php
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/api/chart-data', [DashboardController::class, 'getChartData']);
    Route::get('/api/payment-data', [DashboardController::class, 'getPaymentData']);

    // CRUD Resources
    Route::resource('categories', CategoryController::class);
    Route::resource('menus', MenuController::class);
    Route::resource('orders', OrderController::class);
    
    // File Upload
    Route::post('/menus/upload-image', [MenuController::class, 'uploadImage'])->name('menus.upload-image');
});
```

---

### 3. Cashier Only Routes

**Status**: ✅ Perlu login + role cashier  
**Routes**:
- POS interface, add to cart, checkout, print receipt

**Middleware**: `auth` + `cashier` (middleware custom)

**Kode**:
```php
Route::middleware(['auth', 'cashier'])->group(function () {
    Route::get('/cashier', [CashierController::class, 'index'])->name('cashier.index');
    Route::post('/cashier/add-to-cart', [CashierController::class, 'addToCart'])->name('cashier.add-to-cart');
    Route::put('/cashier/update-cart/{menuId}', [CashierController::class, 'updateCart'])->name('cashier.update-cart');
    Route::delete('/cashier/remove-from-cart/{menuId}', [CashierController::class, 'removeFromCart'])->name('cashier.remove-from-cart');
    Route::get('/cashier/get-cart', [CashierController::class, 'getCart'])->name('cashier.get-cart');
    Route::post('/cashier/checkout', [CashierController::class, 'checkout'])->name('cashier.checkout');
    Route::post('/cashier/clear-cart', [CashierController::class, 'clearCart'])->name('cashier.clear-cart');
    Route::get('/cashier/print-receipt/{orderId}', [CashierController::class, 'printReceipt'])->name('cashier.print-receipt');
});
```

---

## 📝 Complete Route List

### A. Authentication Routes

| Method | URL | Controller | View/Redirect | Auth | Role |
|--------|-----|-----------|---------------|------|------|
| GET | `/login` | AuthController@showLogin | login form | ❌ | - |
| POST | `/login` | AuthController@authenticate | dashboard | ❌ | - |
| GET | `/register` | AuthController@showRegister | register form | ❌ | - |
| POST | `/register` | AuthController@register | login | ❌ | - |
| POST | `/logout` | AuthController@logout | login | ✅ | - |

**Form Fields**:

```html
<!-- Login Form -->
<form method="POST" action="/login">
  <input name="email" type="email" required>
  <input name="password" type="password" required>
  <button type="submit">Login</button>
</form>

<!-- Register Form -->
<form method="POST" action="/register">
  <input name="name" type="text" required>
  <input name="email" type="email" required>
  <input name="password" type="password" required>
  <input name="password_confirmation" type="password" required>
  <button type="submit">Register</button>
</form>
```

---

### B. Dashboard Routes

| Method | URL | Controller | Purpose | Auth | Role |
|--------|-----|-----------|---------|------|------|
| GET | `/` | DashboardController@index | Main dashboard | ✅ | All |
| GET | `/dashboard` | DashboardController@index | Dashboard page | ✅ | All |
| GET | `/api/chart-data` | DashboardController@getChartData | Chart JSON data | ✅ | All |
| GET | `/api/payment-data` | DashboardController@getPaymentData | Payment pie chart | ✅ | All |

**Response Format**:

```javascript
// /api/chart-data (Line Chart)
{
  "labels": ["Nov 01", "Nov 02", ...],
  "data": [500000, 600000, ...]
}

// /api/payment-data (Pie Chart)
{
  "labels": ["cash", "credit_card", "qris"],
  "data": [15, 10, 5]
}
```

---

### C. Category Routes (RESTful)

| Method | URL | Controller | Purpose | Auth | Role |
|--------|-----|-----------|---------|------|------|
| GET | `/categories` | CategoryController@index | List categories | ✅ | All |
| GET | `/categories/create` | CategoryController@create | Create form | ✅ | All |
| POST | `/categories` | CategoryController@store | Save category | ✅ | All |
| GET | `/categories/{id}/edit` | CategoryController@edit | Edit form | ✅ | All |
| PUT/PATCH | `/categories/{id}` | CategoryController@update | Update category | ✅ | All |
| DELETE | `/categories/{id}` | CategoryController@destroy | Delete category | ✅ | All |

**Form Example**:
```html
<!-- Create/Edit Form -->
<form method="POST" action="/categories" (or PATCH /categories/{id})>
  @csrf
  <input name="name" type="text" placeholder="Nama Kategori" required>
  <textarea name="description" placeholder="Deskripsi"></textarea>
  <button type="submit">Simpan</button>
</form>
```

---

### D. Menu Routes (RESTful)

| Method | URL | Controller | Purpose | Auth | Role |
|--------|-----|-----------|---------|------|------|
| GET | `/menus` | MenuController@index | List menus | ✅ | All |
| GET | `/menus/create` | MenuController@create | Create form | ✅ | All |
| POST | `/menus` | MenuController@store | Save menu | ✅ | All |
| GET | `/menus/{id}/edit` | MenuController@edit | Edit form | ✅ | All |
| PUT/PATCH | `/menus/{id}` | MenuController@update | Update menu | ✅ | All |
| DELETE | `/menus/{id}` | MenuController@destroy | Delete menu | ✅ | All |
| POST | `/menus/upload-image` | MenuController@uploadImage | Upload image | ✅ | All |

**Form Example**:
```html
<form method="POST" action="/menus" enctype="multipart/form-data">
  @csrf
  <input name="name" type="text" placeholder="Nama Menu" required>
  <textarea name="description" placeholder="Deskripsi"></textarea>
  <input name="price" type="number" placeholder="Harga" required>
  <select name="category_id" required>
    <option>Pilih Kategori</option>
    @foreach($categories as $cat)
      <option value="{{ $cat->id }}">{{ $cat->name }}</option>
    @endforeach
  </select>
  <input name="image" type="file" accept="image/*">
  <button type="submit">Simpan</button>
</form>
```

---

### E. Order Routes (RESTful)

| Method | URL | Controller | Purpose | Auth | Role |
|--------|-----|-----------|---------|------|------|
| GET | `/orders` | OrderController@index | List orders | ✅ | All |
| GET | `/orders/create` | OrderController@create | Create form | ✅ | All |
| POST | `/orders` | OrderController@store | Save order | ✅ | All |
| GET | `/orders/{id}/edit` | OrderController@edit | Edit form | ✅ | All |
| PUT/PATCH | `/orders/{id}` | OrderController@update | Update order | ✅ | All |
| DELETE | `/orders/{id}` | OrderController@destroy | Delete order | ✅ | All |

**Form Data Structure**:
```javascript
{
  "total_amount": 150000,
  "total_quantity": 3,
  "payment_method": "cash",
  "items": [
    {
      "menu_id": 1,
      "quantity": 1,
      "price": 35000,
      "discount": 0
    },
    {
      "menu_id": 2,
      "quantity": 2,
      "price": 45000,
      "discount": 5000
    }
  ]
}
```

---

### F. Cashier Routes (POS Interface)

| Method | URL | Controller | Purpose | Auth | Role |
|--------|-----|-----------|---------|------|------|
| GET | `/cashier` | CashierController@index | POS interface | ✅ | Cashier |
| POST | `/cashier/add-to-cart` | CashierController@addToCart | Add to cart (AJAX) | ✅ | Cashier |
| PUT | `/cashier/update-cart/{menuId}` | CashierController@updateCart | Update quantity (AJAX) | ✅ | Cashier |
| DELETE | `/cashier/remove-from-cart/{menuId}` | CashierController@removeFromCart | Remove item (AJAX) | ✅ | Cashier |
| GET | `/cashier/get-cart` | CashierController@getCart | Get cart data (AJAX) | ✅ | Cashier |
| POST | `/cashier/checkout` | CashierController@checkout | Process payment (AJAX) | ✅ | Cashier |
| POST | `/cashier/clear-cart` | CashierController@clearCart | Clear cart (AJAX) | ✅ | Cashier |
| GET | `/cashier/print-receipt/{orderId}` | CashierController@printReceipt | Print nota | ✅ | Cashier |

**AJAX Request Example** (JavaScript):
```javascript
// Add to cart
fetch('/cashier/add-to-cart', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('[name=csrf-token]').content
  },
  body: JSON.stringify({
    menu_id: 1,
    quantity: 2
  })
})
.then(res => res.json())
.then(data => console.log(data));

// Checkout
fetch('/cashier/checkout', {
  method: 'POST',
  headers: {...},
  body: JSON.stringify({
    payment_method: 'cash',
    amount_paid: 200000
  })
})
```

---

## 🔒 Middleware Explanation

### 1. `guest` Middleware

**Lokasi**: Built-in Laravel  
**Fungsi**: Hanya allow user yang BELUM login

**Usage**:
```php
Route::middleware('guest')->group(function () {
    Route::get('/login', ...);
    Route::post('/register', ...);
});
```

**Flow**:
- User sudah login? → Redirect ke `/`
- User belum login? → Allow akses

---

### 2. `auth` Middleware

**Lokasi**: Built-in Laravel  
**Fungsi**: Hanya allow user yang sudah login

**Usage**:
```php
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', ...);
    Route::resource('menus', MenuController::class);
});
```

**Flow**:
- User sudah login? → Allow akses
- User belum login? → Redirect ke `/login`

---

### 3. `cashier` Middleware (Custom)

**Lokasi**: `app/Http/Middleware/Cashier.php`

**Fungsi**: Hanya allow user dengan role `cashier`

**Code**:
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cashier
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user() && auth()->user()->role === 'cashier') {
            return $next($request);
        }

        return redirect('/')->with('error', 'Akses hanya untuk kasir');
    }
}
```

**Usage**:
```php
Route::middleware(['auth', 'cashier'])->group(function () {
    Route::get('/cashier', ...);
    Route::post('/cashier/checkout', ...);
});
```

**Flow**:
1. Check user sudah login (`auth` middleware)
2. Check role = 'cashier' (`cashier` middleware)
3. Jika tidak, redirect ke home dengan error message

---

## 📖 Quick Reference Cheatsheet

### Common URL Patterns

```bash
# List semua resources
GET /menus
GET /orders
GET /categories

# View single resource (dengan modal/inline)
GET /menus/{id}/edit
GET /orders/{id}/edit

# Create new resource
GET /menus/create                  # Show form
POST /menus                        # Process form

# Update resource
GET /menus/{id}/edit               # Show form with current data
PUT /menus/{id}                    # Process form
PATCH /menus/{id}                  # Alternative to PUT

# Delete resource
DELETE /menus/{id}                 # Delete via form button

# API Endpoints (JSON response)
GET /api/chart-data
GET /api/payment-data
POST /cashier/checkout (JSON request)
```

---

### HTTP Methods Explained

| Method | Purpose | Body | Cacheable | Example |
|--------|---------|------|-----------|---------|
| **GET** | Retrieve data | ❌ | ✅ | Get menu list |
| **POST** | Create new | ✅ | ❌ | Create order |
| **PUT** | Replace entire resource | ✅ | ❌ | Update menu |
| **PATCH** | Partial update | ✅ | ❌ | Update single field |
| **DELETE** | Remove resource | ❌ | ❌ | Delete menu |

**Note**: Laravel form bisa POST/PATCH/DELETE via:
```html
<form method="POST" action="/menus/1">
  @method('PUT')  <!-- or PATCH/DELETE -->
  @csrf
  ...
</form>
```

---

### Route Model Binding

**Automatic Parameter Injection**:

```php
// Route definition
Route::get('/menus/{id}/edit', [MenuController::class, 'edit']);

// Controller method
public function edit(Menu $menu)  // <-- Auto inject, find by ID
{
    // $menu adalah instance Menu dengan id dari URL
    return view('menus.edit', compact('menu'));
}
```

**Benefit**: Tidak perlu manual `Menu::find($id)` di method

---

### Named Routes

Routes punya nama untuk reusability:

```php
// Definition
Route::get('/menus', [...])name('menus.index');
Route::get('/menus/{id}/edit', [...]) ->name('menus.edit');

// Usage di Controller (redirect)
return redirect()->route('menus.index');
return redirect()->route('menus.edit', ['id' => $menu->id]);

// Usage di Blade View (generate URL)
<a href="{{ route('menus.index') }}">Menu List</a>
<a href="{{ route('menus.edit', $menu) }}">Edit</a>
```

---

### Route Resource Shortcut

```php
// Manual routes
Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
Route::get('/menus/create', [MenuController::class, 'create'])->name('menus.create');
Route::post('/menus', [MenuController::class, 'store'])->name('menus.store');
Route::get('/menus/{menu}/edit', [MenuController::class, 'edit'])->name('menus.edit');
Route::put('/menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');

// Shortcut dengan Resource
Route::resource('menus', MenuController::class);
```

**Resource Methods Generated**:
| Method | Route | Action |
|--------|-------|--------|
| index | GET /menus | List |
| create | GET /menus/create | Form create |
| store | POST /menus | Save |
| edit | GET /menus/{id}/edit | Form edit |
| update | PUT /menus/{id} | Update |
| destroy | DELETE /menus/{id} | Delete |
| show | GET /menus/{id} | View detail (optional) |

---

### Flash Messages

**Set di Controller**:
```php
return redirect()->route('menus.index')
    ->with('success', 'Menu berhasil ditambahkan');

return redirect()->back()
    ->withErrors(['email' => 'Email tidak valid']);
```

**Display di Blade**:
```blade
@if($message = Session::get('success'))
  <div class="alert alert-success">{{ $message }}</div>
@endif

@if($errors->any())
  <div class="alert alert-danger">
    @foreach ($errors->all() as $error)
      <div>{{ $error }}</div>
    @endforeach
  </div>
@endif
```

---

### Form Helpers

```blade
<!-- Generate URL -->
{{ route('menus.index') }}
{{ url('/menus') }}
{{ asset('css/app.css') }}

<!-- CSRF Token -->
@csrf
<!-- OR -->
<input name="_token" value="{{ csrf_token() }}">

<!-- Method Spoofing -->
@method('PUT')    <!-- Equivalent: <input name="_method" value="PUT"> -->

<!-- Old Input (repopulate form on validation error) -->
<input name="name" value="{{ old('name') }}">
```

---

**Selesai! Quick reference lengkap untuk routes. 🎯**

