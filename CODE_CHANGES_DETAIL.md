═══════════════════════════════════════════════════════════════════
DETAILED CODE CHANGES - SISTEM STOK
═══════════════════════════════════════════════════════════════════

📌 FILE 1: app/Models/Menu.php
═══════════════════════════════════════════════════════════════════

PERUBAHAN:
✏️ Tambah 'stock' ke $fillable
✏️ Tambah 3 helper methods

BEFORE:

```php
protected $fillable = [
    'name',
    'description',
    'price',
    'image_url',
    'category_id',
    'sold_quantity',
];
```

AFTER:

```php
protected $fillable = [
    'name',
    'description',
    'price',
    'image_url',
    'category_id',
    'sold_quantity',
    'stock',  // ← ADDED
];
```

TAMBAH METHODS:

```php
// Cek apakah stok cukup
public function hasEnoughStock($quantity)
{
    return $this->stock >= $quantity;
}

// Kurangi stok
public function decreaseStock($quantity)
{
    $this->stock -= $quantity;
    $this->save();
}

// Tambah stok
public function increaseStock($quantity)
{
    $this->stock += $quantity;
    $this->save();
}
```

═══════════════════════════════════════════════════════════════════
📌 FILE 2: app/Http/Controllers/CashierController.php
═══════════════════════════════════════════════════════════════════

PERUBAHAN:
✏️ addToCart() - Tambah validasi stok
✏️ checkout() - Tambah validasi stok + decreaseStock()

SECTION 1: addToCart()

BEFORE:

```php
public function addToCart(Request $request)
{
    $request->validate([
        'menu_id' => 'required|exists:menus,id',
        'quantity' => 'required|integer|min:1',
    ]);

    $menu = Menu::findOrFail($request->menu_id);
    $cart = session()->get('cashier_cart', []);

    if (isset($cart[$menu->id])) {
        $cart[$menu->id]['quantity'] += $request->quantity;
    } else {
        $cart[$menu->id] = [
            'id' => $menu->id,
            'name' => $menu->name,
            'price' => $menu->price,
            'quantity' => $request->quantity,
            'image_url' => $menu->image_url,
            'category' => $menu->category->name ?? 'N/A',
        ];
    }
    // ... rest of code
}
```

AFTER (tambah validasi stock):

```php
public function addToCart(Request $request)
{
    $request->validate([
        'menu_id' => 'required|exists:menus,id',
        'quantity' => 'required|integer|min:1',
    ]);

    $menu = Menu::findOrFail($request->menu_id);

    // ← TAMBAH: Validasi stok
    if (!$menu->hasEnoughStock($request->quantity)) {
        return response()->json([
            'success' => false,
            'message' => "Stok {$menu->name} tidak cukup. Stok tersedia: {$menu->stock}",
        ], 422);
    }
    // ← END TAMBAH

    $cart = session()->get('cashier_cart', []);

    if (isset($cart[$menu->id])) {
        $newQuantity = $cart[$menu->id]['quantity'] + $request->quantity;

        // ← TAMBAH: Validasi total stok
        if (!$menu->hasEnoughStock($newQuantity)) {
            return response()->json([
                'success' => false,
                'message' => "Stok {$menu->name} tidak cukup. Stok tersedia: {$menu->stock}, diminta total: {$newQuantity}",
            ], 422);
        }
        // ← END TAMBAH

        $cart[$menu->id]['quantity'] = $newQuantity;
    } else {
        $cart[$menu->id] = [
            'id' => $menu->id,
            'name' => $menu->name,
            'price' => $menu->price,
            'quantity' => $request->quantity,
            'image_url' => $menu->image_url,
            'category' => $menu->category->name ?? 'N/A',
        ];
    }
    // ... rest of code
}
```

SECTION 2: checkout()

BEFORE (snippet):

```php
// Create order items
foreach ($cart as $item) {
    $discountPerItem = ($item['price'] * $item['quantity'] * $discountPercentage) / 100;

    OrderItem::create([
        'order_id' => $order->id,
        'menu_id' => $item['id'],
        'quantity' => $item['quantity'],
        'price' => $item['price'],
        'discount' => $discountPerItem,
    ]);

    // Update menu sold quantity
    Menu::find($item['id'])->increment('sold_quantity', $item['quantity']);
}
```

AFTER (tambah validasi + decreaseStock):

```php
// ← TAMBAH: Validasi stok untuk semua item
foreach ($cart as $item) {
    $menu = Menu::find($item['id']);
    if (!$menu->hasEnoughStock($item['quantity'])) {
        return response()->json([
            'success' => false,
            'message' => "Stok {$menu->name} tidak cukup. Stok tersedia: {$menu->stock}",
        ], 422);
    }
}
// ← END TAMBAH

try {
    // ... calculate totals ...

    // Create order
    $order = Order::create([...]);

    // Create order items and decrease stock
    foreach ($cart as $item) {
        $discountPerItem = ($item['price'] * $item['quantity'] * $discountPercentage) / 100;

        OrderItem::create([
            'order_id' => $order->id,
            'menu_id' => $item['id'],
            'quantity' => $item['quantity'],
            'price' => $item['price'],
            'discount' => $discountPerItem,
        ]);

        // Update menu sold quantity and decrease stock
        $menu = Menu::find($item['id']);
        $menu->increment('sold_quantity', $item['quantity']);
        $menu->decreaseStock($item['quantity']);  // ← TAMBAH
    }
    // ... rest of code
}
```

═══════════════════════════════════════════════════════════════════
📌 FILE 3: app/Http/Controllers/OrderController.php
═══════════════════════════════════════════════════════════════════

PERUBAHAN:
✏️ store() - Tambah validasi stok + decreaseStock()

BEFORE (snippet):

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

    $order = Order::create([...]);

    foreach ($validated['items'] as $item) {
        OrderItem::create([...]);
        $menu = Menu::find($item['menu_id']);
        $menu->increment('sold_quantity', $item['quantity']);
    }

    return redirect()->route('orders.index')->with('success', 'Order berhasil dibuat');
}
```

AFTER (tambah validasi + decreaseStock):

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

    // ← TAMBAH: Validasi stok untuk semua item
    foreach ($validated['items'] as $item) {
        $menu = Menu::find($item['menu_id']);

        if (!$menu->hasEnoughStock($item['quantity'])) {
            return back()->withErrors([
                'stock_error' => "Stok {$menu->name} tidak cukup. Stok tersedia: {$menu->stock}, diminta: {$item['quantity']}"
            ])->withInput();
        }
    }
    // ← END TAMBAH

    $order = Order::create([...]);

    foreach ($validated['items'] as $item) {
        OrderItem::create([...]);

        $menu = Menu::find($item['menu_id']);
        $menu->increment('sold_quantity', $item['quantity']);
        $menu->decreaseStock($item['quantity']);  // ← TAMBAH
    }

    return redirect()->route('orders.index')->with('success', 'Order berhasil dibuat');
}
```

═══════════════════════════════════════════════════════════════════
📌 FILE 4: resources/views/cashier/index.blade.php
═══════════════════════════════════════════════════════════════════

PERUBAHAN:
✏️ Tambah stock badge
✏️ Disable tombol "Tambah" jika stok habis

BEFORE (menu card section):

```blade
@foreach($menus as $menu)
    <div class="menu-card bg-white rounded-lg shadow hover:shadow-lg transition cursor-pointer"
         data-menu-id="{{ $menu->id }}" data-category="{{ $menu->category_id }}">

        <!-- Image -->
        <div class="relative bg-gray-200 h-24 overflow-hidden rounded-t-lg">
            @if($menu->image_url)
                <img src="{{ $menu->image_url }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center bg-gray-300">
                    <i class="fas fa-image text-gray-400 text-2xl"></i>
                </div>
            @endif
        </div>

        <!-- Content -->
        <div class="p-2">
            <h3 class="font-semibold text-gray-800 mb-1 text-sm line-clamp-1">{{ $menu->name }}</h3>
            <p class="text-xs text-gray-500 mb-1">{{ $menu->category->name ?? 'N/A' }}</p>
            <p class="text-gray-600 text-xs mb-2 line-clamp-1">{{ $menu->description ?? '-' }}</p>

            <div class="flex flex-col gap-1">
                <p class="text-sm font-bold text-orange-600">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                <button class="add-to-cart-btn bg-blue-500 text-white px-2 py-1 rounded-lg
                              hover:bg-blue-600 transition text-xs w-full"
                        data-menu-id="{{ $menu->id }}" data-menu-name="{{ $menu->name }}">
                    <i class="fas fa-shopping-cart mr-1"></i> Tambah
                </button>
            </div>
        </div>
    </div>
@endforeach
```

AFTER (tambah stock badge & conditional):

```blade
@foreach($menus as $menu)
    <div class="menu-card bg-white rounded-lg shadow hover:shadow-lg transition cursor-pointer relative"
         data-menu-id="{{ $menu->id }}" data-category="{{ $menu->category_id }}"
         @if($menu->stock <= 0) style="opacity: 0.6;" @endif>

        <!-- Image -->
        <div class="relative bg-gray-200 h-24 overflow-hidden rounded-t-lg">
            @if($menu->image_url)
                <img src="{{ $menu->image_url }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center bg-gray-300">
                    <i class="fas fa-image text-gray-400 text-2xl"></i>
                </div>
            @endif

            <!-- ← TAMBAH: Stock Badge -->
            <div class="absolute top-1 right-1 px-2 py-1 rounded text-xs font-semibold
                        @if($menu->stock <= 0) bg-red-500 text-white
                        @elseif($menu->stock <= 5) bg-yellow-500 text-white
                        @else bg-green-500 text-white @endif">
                Stok: {{ $menu->stock }}
            </div>
            <!-- ← END TAMBAH -->
        </div>

        <!-- Content -->
        <div class="p-2">
            <h3 class="font-semibold text-gray-800 mb-1 text-sm line-clamp-1">{{ $menu->name }}</h3>
            <p class="text-xs text-gray-500 mb-1">{{ $menu->category->name ?? 'N/A' }}</p>
            <p class="text-gray-600 text-xs mb-2 line-clamp-1">{{ $menu->description ?? '-' }}</p>

            <div class="flex flex-col gap-1">
                <p class="text-sm font-bold text-orange-600">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>

                <!-- ← TAMBAH: Conditional button & disabled -->
                <button class="add-to-cart-btn bg-blue-500 text-white px-2 py-1 rounded-lg
                              hover:bg-blue-600 transition text-xs w-full"
                        data-menu-id="{{ $menu->id }}" data-menu-name="{{ $menu->name }}"
                        @if($menu->stock <= 0) disabled style="opacity: 0.5; cursor: not-allowed;" @endif>
                    @if($menu->stock <= 0)
                        <i class="fas fa-times mr-1"></i> Habis
                    @else
                        <i class="fas fa-shopping-cart mr-1"></i> Tambah
                    @endif
                </button>
                <!-- ← END TAMBAH -->
            </div>
        </div>
    </div>
@endforeach
```

═══════════════════════════════════════════════════════════════════
📌 FILE 5: database/migrations/2024_12_02_000001_add_stock_to_menus_table.php
═══════════════════════════════════════════════════════════════════

FILE BARU - Migration untuk tambah kolom stock

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->integer('stock')->default(0)->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('stock');
        });
    }
};
```

═══════════════════════════════════════════════════════════════════
📌 FILE 6: database/seeders/AddStockToMenusSeeder.php
═══════════════════════════════════════════════════════════════════

FILE BARU - Seeder untuk set default stok

```php
<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class AddStockToMenusSeeder extends Seeder
{
    public function run(): void
    {
        // Set default stock untuk semua menu yang belum punya stok
        Menu::where('stock', 0)->update(['stock' => 10]);

        $this->command->info('Stok default telah ditambahkan ke menu!');
    }
}
```

═══════════════════════════════════════════════════════════════════
✅ SEMUA PERUBAHAN SELESAI!
═══════════════════════════════════════════════════════════════════

RINGKASAN:

-   Model: 1 file (Menu.php) - Tambah $fillable + 3 methods
-   Controller: 2 files - Tambah validasi stok di 2 controller
-   View: 1 file - Tambah badge stok & conditional button
-   Migration: 1 file baru - Tambah kolom stock
-   Seeder: 1 file baru - Set default stok = 10

Total:
✅ 3 files modified
✅ 3 files created
✅ All changes saved & ready

═══════════════════════════════════════════════════════════════════
