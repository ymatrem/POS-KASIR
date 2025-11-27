# 💻 DOKUMENTASI CONTROLLERS & BUSINESS LOGIC

**Bahasa**: Bahasa Indonesia  
**Lokasi**: `app/Http/Controllers/`

---

## 📋 DAFTAR ISI

1. [Overview Controllers](#overview-controllers)
2. [AuthController](#authcontroller)
3. [DashboardController](#dashboardcontroller)
4. [MenuController](#menucontroller)
5. [CategoryController](#categorycontroller)
6. [OrderController](#ordercontroller)
7. [CashierController](#cashiercontroller)

---

## 🎯 Overview Controllers

### Apa itu Controller?

Controller adalah class PHP yang bertugas:
- Menerima request dari user
- Memvalidasi input
- Memanggil model untuk query database
- Mengembalikan response (view atau JSON)

### Struktur Dasar Controller

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;

class MenuController extends Controller
{
    // Method untuk menampilkan list
    public function index() {}
    
    // Method untuk menampilkan form create
    public function create() {}
    
    // Method untuk menyimpan data baru
    public function store(Request $request) {}
    
    // Method untuk menampilkan form edit
    public function edit($id) {}
    
    // Method untuk update data
    public function update(Request $request, $id) {}
    
    // Method untuk delete data
    public function destroy($id) {}
}
```

### RESTful Convention

| HTTP Method | Endpoint | Controller Method | Tujuan |
|---|---|---|---|
| GET | `/menus` | `index()` | Tampilkan list |
| GET | `/menus/create` | `create()` | Tampilkan form create |
| POST | `/menus` | `store()` | Simpan data baru |
| GET | `/menus/{id}/edit` | `edit($id)` | Tampilkan form edit |
| PUT/PATCH | `/menus/{id}` | `update($id)` | Update data |
| DELETE | `/menus/{id}` | `destroy($id)` | Hapus data |

---

## 🔐 AuthController

**File**: `app/Http/Controllers/AuthController.php`

### Fungsi

Controller untuk menangani autentikasi user (login, register, logout).

### Method: showLogin()

**Route**: `GET /login`

```php
public function showLogin()
{
    return view('auth.login');
}
```

**Fungsi**:
- Menampilkan halaman login
- View: `resources/views/auth/login.blade.php`

**Response**:
- HTML form login dengan email & password input

---

### Method: authenticate()

**Route**: `POST /login`

```php
public function authenticate(Request $request)
{
    $validated = $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:6',
    ]);

    if (Auth::attempt($validated)) {
        $request->session()->regenerate();
        return redirect()->intended('/')->with('success', 'Login berhasil!');
    }

    return back()
        ->withInput($request->only('email'))
        ->withErrors(['email' => 'Email atau password salah.']);
}
```

**Penjelasan**:

1. **Validasi Input**:
   - `email`: Required, harus format email
   - `password`: Required, minimal 6 karakter

2. **Auth::attempt()**: 
   - Mencari user dengan email
   - Verify password dengan hash
   - Jika cocok, set session/cookie
   - Return boolean (true/false)

3. **Success Flow**:
   - `session()->regenerate()`: Regenerate session ID (security)
   - `redirect()->intended()`: Redirect ke halaman yang diminta sebelum login
   - Jika tidak ada, default redirect ke `/`
   - Flash message success

4. **Failed Flow**:
   - `back()`: Redirect kembali ke form login
   - `withInput()`: Kembalikan input yang dikirim (except password)
   - `withErrors()`: Kirim error message

---

### Method: showRegister()

**Route**: `GET /register`

```php
public function showRegister()
{
    return view('auth.register');
}
```

**Fungsi**:
- Menampilkan halaman register
- View: `resources/views/auth/register.blade.php`

---

### Method: register()

**Route**: `POST /register`

```php
public function register(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6|confirmed',
        'password_confirmation' => 'required|same:password',
    ]);

    User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role' => 'cashier', // Default role
    ]);

    return redirect('/login')
        ->with('success', 'Registrasi berhasil! Silakan login.');
}
```

**Penjelasan**:

1. **Validasi**:
   - `unique:users`: Email belum terdaftar
   - `confirmed`: Verifikasi password_confirmation cocok
   - `same:password`: password_confirmation = password

2. **Hash Password**:
   - `Hash::make()`: Enkripsi password menggunakan bcrypt
   - Password tidak pernah disimpan plaintext

3. **Create User**:
   - Default role: `cashier`
   - Email & password diisi dari input

4. **Response**:
   - Redirect ke login dengan success message

---

### Method: logout()

**Route**: `POST /logout`

```php
public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    
    return redirect('/login')->with('success', 'Logout berhasil.');
}
```

**Penjelasan**:
- `Auth::logout()`: Hapus session user
- `invalidate()`: Invalidate session
- `regenerateToken()`: Generate CSRF token baru (security)

---

## 📊 DashboardController

**File**: `app/Http/Controllers/DashboardController.php`

### Fungsi

Controller untuk menampilkan dashboard analytics dengan statistik & chart.

### Method: index()

**Route**: `GET /` atau `GET /dashboard`

```php
public function index()
{
    $startDate = Carbon::now()->subDays(30);
    $endDate = Carbon::now();

    // Total Revenue
    $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
        ->where('status', 'completed')
        ->sum('total_amount');

    // Total Orders
    $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])
        ->where('status', 'completed')
        ->count();

    // Average Sale
    $averageSale = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

    // Payment Methods Distribution
    $paymentMethods = Order::whereBetween('created_at', [$startDate, $endDate])
        ->where('status', 'completed')
        ->selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as total')
        ->groupBy('payment_method')
        ->get();

    // Popular Menu Items
    $popularMenus = Menu::orderBy('sold_quantity', 'desc')->take(5)->get();

    // Sales data for chart (last 30 days)
    $salesByDate = Order::whereBetween('created_at', [$startDate, $endDate])
        ->where('status', 'completed')
        ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total, COUNT(*) as count')
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    return view('dashboard.index', compact(
        'totalRevenue',
        'totalOrders',
        'averageSale',
        'paymentMethods',
        'popularMenus',
        'salesByDate'
    ));
}
```

**Penjelasan**:

1. **Date Range**:
   - `Carbon::now()->subDays(30)`: 30 hari yang lalu
   - `Carbon::now()`: Hari ini
   - Filter semua query untuk 30 hari terakhir

2. **Total Revenue**:
   ```php
   $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
       ->where('status', 'completed')
       ->sum('total_amount');
   ```
   - Query: SELECT SUM(total_amount) FROM orders WHERE...
   - Result: Angka single (ex: 5000000)

3. **Total Orders**:
   ```php
   $totalOrders = Order::...->count();
   ```
   - Query: SELECT COUNT(*) FROM orders WHERE...
   - Result: Angka single (ex: 42)

4. **Average Sale**:
   ```php
   $averageSale = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
   ```
   - Rata-rata revenue per order
   - Guard clause: check jika totalOrders > 0 untuk prevent division by zero

5. **Payment Methods Distribution**:
   ```php
   $paymentMethods = Order::...
       ->selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as total')
       ->groupBy('payment_method')
       ->get();
   ```
   - Raw SQL SELECT: payment_method, COUNT(*), SUM(total_amount)
   - GROUP BY payment_method
   - Result: Collection
     ```
     [
       {"payment_method": "cash", "count": 15, "total": 2000000},
       {"payment_method": "credit_card", "count": 10, "total": 1500000},
       ...
     ]
     ```

6. **Popular Menus**:
   ```php
   $popularMenus = Menu::orderBy('sold_quantity', 'desc')->take(5)->get();
   ```
   - ORDER BY sold_quantity DESC
   - LIMIT 5 (top 5 paling laku)

7. **Sales by Date**:
   ```php
   $salesByDate = Order::...
       ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total, COUNT(*) as count')
       ->groupBy('date')
       ->orderBy('date')
       ->get();
   ```
   - GROUP BY tanggal (DATE function)
   - Result: 
     ```
     [
       {"date": "2025-11-01", "total": 500000, "count": 5},
       {"date": "2025-11-02", "total": 600000, "count": 6},
       ...
     ]
     ```

8. **Return View**:
   - Pass semua data ke view
   - View: `resources/views/dashboard/index.blade.php`

---

### Method: getChartData()

**Route**: `GET /api/chart-data`

**Return**: JSON

```php
public function getChartData()
{
    $startDate = Carbon::now()->subDays(30);
    $endDate = Carbon::now();

    $salesByDate = Order::whereBetween('created_at', [$startDate, $endDate])
        ->where('status', 'completed')
        ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    if ($salesByDate->isEmpty()) {
        $labels = [];
        $data = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M d');
            $data[] = 0;
        }
    } else {
        $labels = $salesByDate->pluck('date')
            ->map(fn($d) => date('M d', strtotime($d)))
            ->toArray();
        $data = $salesByDate->pluck('total')->toArray();
    }

    return response()->json([
        'labels' => $labels,
        'data' => $data,
    ]);
}
```

**Fungsi**:
- Digunakan oleh Chart.js di frontend untuk draw chart
- Return JSON dengan format:
  ```json
  {
    "labels": ["Nov 01", "Nov 02", ...],
    "data": [500000, 600000, ...]
  }
  ```

**Penjelasan**:

1. **Jika ada data**:
   - Extract tanggal & total
   - Format tanggal ke "Nov 01" format
   - Return sebagai array

2. **Jika tidak ada data**:
   - Generate 30 hari dengan value 0
   - Untuk smooth chart display

---

### Method: getPaymentData()

**Route**: `GET /api/payment-data`

**Return**: JSON

```php
public function getPaymentData()
{
    $startDate = Carbon::now()->subDays(30);
    $endDate = Carbon::now();

    $paymentMethods = Order::whereBetween('created_at', [$startDate, $endDate])
        ->where('status', 'completed')
        ->selectRaw('payment_method, COUNT(*) as count')
        ->groupBy('payment_method')
        ->get();

    $labels = $paymentMethods->pluck('payment_method')->toArray();
    $data = $paymentMethods->pluck('count')->toArray();

    return response()->json([
        'labels' => $labels,
        'data' => $data,
    ]);
}
```

**Fungsi**:
- Data untuk pie chart payment methods
- Return JSON:
  ```json
  {
    "labels": ["cash", "credit_card", "qris"],
    "data": [15, 10, 5]
  }
  ```

---

## 🍽️ MenuController

**File**: `app/Http/Controllers/MenuController.php`

### Method: index()

**Route**: `GET /menus`

```php
public function index()
{
    $menus = Menu::with('category')->paginate(10);
    return view('menus.index', compact('menus'));
}
```

**Penjelasan**:
- `with('category')`: Eager loading kategori (prevent N+1)
- `paginate(10)`: Show 10 menus per page
- Return view dengan data menus

---

### Method: create()

**Route**: `GET /menus/create`

```php
public function create()
{
    $categories = Category::all();
    return view('menus.create', compact('categories'));
}
```

**Fungsi**:
- Tampilkan form create menu
- Load semua kategori untuk dropdown

---

### Method: store()

**Route**: `POST /menus`

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'category_id' => 'required|exists:categories,id',
    ]);

    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('menus', 'public');
        $validated['image_url'] = Storage::url($path);
    } else {
        unset($validated['image_url']);
    }

    unset($validated['image']);

    Menu::create($validated);
    return redirect()->route('menus.index')
        ->with('success', 'Menu berhasil ditambahkan');
}
```

**Penjelasan**:

1. **Validasi**:
   - `exists:categories,id`: Kategori harus ada di tabel categories
   - `image`: File harus image (jpeg, png, jpg, gif)
   - `max:2048`: Max size 2MB

2. **File Upload**:
   - `store('menus', 'public')`: Simpan ke storage/app/public/menus/
   - Return path: `menus/abc123.jpg`
   - `Storage::url()`: Generate URL: `/storage/menus/abc123.jpg`

3. **Create Menu**:
   - `Menu::create()`: Mass assignment create
   - Field yang di-create: name, description, price, image_url, category_id

4. **Response**:
   - Redirect ke menu list
   - Flash success message

---

### Method: edit()

**Route**: `GET /menus/{id}/edit`

```php
public function edit(Menu $menu)
{
    $categories = Category::all();
    return view('menus.edit', compact('menu', 'categories'));
}
```

**Penjelasan**:
- `Menu $menu`: Route model binding (auto find by ID)
- Load form dengan data menu existing

---

### Method: update()

**Route**: `PUT /menus/{id}`

```php
public function update(Request $request, Menu $menu)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'category_id' => 'required|exists:categories,id',
    ]);

    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('menus', 'public');
        $validated['image_url'] = Storage::url($path);
    } else {
        unset($validated['image_url']);
    }

    unset($validated['image']);

    $menu->update($validated);
    return redirect()->route('menus.index')
        ->with('success', 'Menu berhasil diperbarui');
}
```

**Flow sama seperti store(), tapi:**
- Update existing menu (bukan create baru)
- Old image tidak dihapus otomatis (bisa diimprove)

---

### Method: destroy()

**Route**: `DELETE /menus/{id}`

```php
public function destroy(Menu $menu)
{
    $menu->delete();
    return redirect()->route('menus.index')
        ->with('success', 'Menu berhasil dihapus');
}
```

**Penjelasan**:
- Delete menu
- On delete cascade: Jika ada order items, akan error (RESTRICT)
- Redirect dengan success message

---

## 📝 CategoryController

Struktur mirip MenuController (index, create, store, edit, update, destroy).

### Key Differences:

1. **Model**: Category (bukan Menu)
2. **Slug Generation**: Di model boot method
   ```php
   static::creating(function ($model) {
       $model->slug = Str::slug($model->name);
   });
   ```
3. **No File Upload**: Category tidak punya image
4. **Relationship**: `hasMany('menus')` - Check sebelum delete

---

## 📦 OrderController

**File**: `app/Http/Controllers/OrderController.php`

### Method: index()

```php
public function index()
{
    $orders = Order::with('items')->paginate(10);
    return view('orders.index', compact('orders'));
}
```

- `with('items')`: Eager load order items

---

### Method: create()

```php
public function create()
{
    $menus = Menu::all();
    return view('orders.create', compact('menus'));
}
```

- Load semua menus untuk dropdown/selection

---

### Method: store()

**Route**: `POST /orders`

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'total_amount' => 'required|numeric|min:0',
        'total_quantity' => 'required|integer|min:1',
        'payment_method' => 'required|in:cash,credit_card,qris',
        'items' => 'required|array|min:1',
        'items.*.menu_id' => 'required|exists:menus,id',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.price' => 'required|numeric|min:0',
        'items.*.discount' => 'nullable|numeric|min:0',
    ]);

    $order = Order::create([
        'order_number' => 'ORD-' . time(),
        'total_amount' => $validated['total_amount'],
        'total_quantity' => $validated['total_quantity'],
        'payment_method' => $validated['payment_method'],
        'status' => 'completed',
        'completed_at' => now(),
    ]);

    foreach ($validated['items'] as $item) {
        OrderItem::create([
            'order_id' => $order->id,
            'menu_id' => $item['menu_id'],
            'quantity' => $item['quantity'],
            'price' => $item['price'],
            'discount' => $item['discount'] ?? 0,
        ]);

        $menu = Menu::find($item['menu_id']);
        $menu->increment('sold_quantity', $item['quantity']);
    }

    return redirect()->route('orders.index')
        ->with('success', 'Order berhasil dibuat');
}
```

**Penjelasan**:

1. **Array Validation** (`items.*`):
   - Validasi array items
   - Setiap item harus punya menu_id, quantity, price
   - `items.*.menu_id`: Index 0, 1, 2, ... dari array items

2. **Order Creation**:
   - `order_number`: 'ORD-' + current timestamp
   - Status: 'completed' (sudah dibayar)
   - completed_at: Tanggal sekarang

3. **Items Loop**:
   ```php
   foreach ($validated['items'] as $item) {
       // 1. Buat order item record
       OrderItem::create([...]);
       
       // 2. Update sold_quantity di menu
       Menu::find($item['menu_id'])->increment('sold_quantity', $item['quantity']);
   }
   ```

4. **Update Menu Popularity**:
   - `increment()`: Tambah field value
   - Digunakan untuk "popular menu" ranking

---

### Method: edit() & update()

Edit & update order (mirip menu).

---

### Method: destroy()

```php
public function destroy(Order $order)
{
    $order->delete();
    return redirect()->route('orders.index')
        ->with('success', 'Order berhasil dihapus');
}
```

- Delete order
- ON DELETE CASCADE: Items & transaction terhapus otomatis

---

## 💳 CashierController

**File**: `app/Http/Controllers/CashierController.php`

Controller untuk POS (Point of Sale) interface kasir.

### Method: index()

**Route**: `GET /cashier`

```php
public function index()
{
    $menus = Menu::with('category')->get();
    return view('cashier.index', compact('menus'));
}
```

- Load semua menu untuk ditampilkan di POS interface

---

### Method: addToCart()

**Route**: `POST /cashier/add-to-cart`

```php
public function addToCart(Request $request)
{
    $validated = $request->validate([
        'menu_id' => 'required|exists:menus,id',
        'quantity' => 'required|integer|min:1',
    ]);

    $menu = Menu::find($validated['menu_id']);
    
    $cart = session()->get('cart', []);
    
    if (isset($cart[$validated['menu_id']])) {
        $cart[$validated['menu_id']]['quantity'] += $validated['quantity'];
    } else {
        $cart[$validated['menu_id']] = [
            'menu_id' => $menu->id,
            'name' => $menu->name,
            'price' => $menu->price,
            'quantity' => $validated['quantity'],
        ];
    }
    
    session()->put('cart', $cart);
    
    return response()->json(['success' => true, 'message' => 'Item ditambahkan ke keranjang']);
}
```

**Penjelasan**:
- Session-based cart (not database)
- Format cart:
  ```php
  [
    '1' => ['menu_id' => 1, 'name' => 'Burger', 'price' => 35000, 'quantity' => 2],
    '2' => ['menu_id' => 2, 'name' => 'Pizza', 'price' => 65000, 'quantity' => 1],
  ]
  ```

---

### Method: updateCart()

**Route**: `PUT /cashier/update-cart/{menuId}`

```php
public function updateCart(Request $request, $menuId)
{
    $validated = $request->validate([
        'quantity' => 'required|integer|min:0',
    ]);

    $cart = session()->get('cart', []);
    
    if ($validated['quantity'] == 0) {
        unset($cart[$menuId]);
    } else {
        $cart[$menuId]['quantity'] = $validated['quantity'];
    }
    
    session()->put('cart', $cart);
    
    return response()->json(['success' => true]);
}
```

---

### Method: removeFromCart()

**Route**: `DELETE /cashier/remove-from-cart/{menuId}`

```php
public function removeFromCart($menuId)
{
    $cart = session()->get('cart', []);
    unset($cart[$menuId]);
    session()->put('cart', $cart);
    
    return response()->json(['success' => true]);
}
```

---

### Method: getCart()

**Route**: `GET /cashier/get-cart`

```php
public function getCart()
{
    $cart = session()->get('cart', []);
    
    $total = 0;
    $totalQuantity = 0;
    
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
        $totalQuantity += $item['quantity'];
    }
    
    return response()->json([
        'cart' => $cart,
        'total' => $total,
        'totalQuantity' => $totalQuantity,
        'count' => count($cart),
    ]);
}
```

---

### Method: checkout()

**Route**: `POST /cashier/checkout`

```php
public function checkout(Request $request)
{
    $validated = $request->validate([
        'payment_method' => 'required|in:cash,credit_card,qris',
        'amount_paid' => 'required|numeric|min:0',
    ]);

    $cart = session()->get('cart', []);
    
    if (empty($cart)) {
        return response()->json(['success' => false, 'message' => 'Keranjang kosong'], 400);
    }

    // Calculate total
    $total = 0;
    $totalQuantity = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
        $totalQuantity += $item['quantity'];
    }

    // Create order
    $order = Order::create([
        'order_number' => 'ORD-' . time(),
        'total_amount' => $total,
        'total_quantity' => $totalQuantity,
        'payment_method' => $validated['payment_method'],
        'status' => 'completed',
        'completed_at' => now(),
    ]);

    // Create items
    foreach ($cart as $item) {
        OrderItem::create([
            'order_id' => $order->id,
            'menu_id' => $item['menu_id'],
            'quantity' => $item['quantity'],
            'price' => $item['price'],
            'discount' => 0,
        ]);

        Menu::find($item['menu_id'])->increment('sold_quantity', $item['quantity']);
    }

    // Create transaction
    Transaction::create([
        'order_id' => $order->id,
        'payment_method' => $validated['payment_method'],
        'amount_paid' => $validated['amount_paid'],
        'change_amount' => $validated['amount_paid'] - $total,
        'status' => 'success',
        'paid_at' => now(),
    ]);

    // Clear cart
    session()->forget('cart');

    return response()->json(['success' => true, 'order_id' => $order->id]);
}
```

**Flow Checkout**:
1. Validasi payment method & amount paid
2. Calculate total dari cart items
3. Create order header
4. Create order items (loop cart)
5. Update menu sold_quantity
6. Create transaction (payment record)
7. Clear session cart
8. Return success dengan order ID

---

**Selesai! Documentasi controllers lengkap. 🎉**

