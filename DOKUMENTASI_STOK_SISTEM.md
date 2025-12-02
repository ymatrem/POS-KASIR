# 📦 Sistem Stok Menu - Dokumentasi

## Fitur yang Ditambahkan

### 1. **Kolom Stok di Database**

-   Migration: `2024_12_02_000001_add_stock_to_menus_table.php`
-   Kolom `stock` ditambahkan ke table `menus` dengan default value 0

### 2. **Model Menu - Helper Methods**

#### `hasEnoughStock($quantity)`

```php
// Mengecek apakah stok cukup
if ($menu->hasEnoughStock(5)) {
    // Stok cukup untuk 5 unit
}
```

#### `decreaseStock($quantity)`

```php
// Mengurangi stok saat transaksi
$menu->decreaseStock(3); // Kurangi 3 unit
```

#### `increaseStock($quantity)`

```php
// Menambah stok (untuk return atau restocking)
$menu->increaseStock(5); // Tambah 5 unit
```

### 3. **Validasi di CashierController**

#### addToCart() - Validasi saat tambah item

-   Mengecek stok saat item ditambahkan ke keranjang
-   Mengecek total stok jika item sudah ada di keranjang
-   Return error 422 jika stok tidak cukup

```json
{
    "success": false,
    "message": "Stok Nasi Kuning tidak cukup. Stok tersedia: 5"
}
```

#### checkout() - Validasi final sebelum transaksi

-   Mengecek semua item di keranjang
-   Mengurangi stok secara otomatis saat checkout berhasil
-   Update `sold_quantity` bersamaan

### 4. **Tampilan di Cashier Interface**

#### Stock Badge

-   **Hijau (🟢)**: Stok cukup (> 5 unit)
-   **Kuning (🟡)**: Stok terbatas (1-5 unit)
-   **Merah (🔴)**: Stok habis (0 unit)

#### Button Tambah

-   Disabled jika stok habis
-   Menampilkan "Habis" dan "Tambah" sesuai kondisi stok

### 5. **Validasi di OrderController**

Jika membuat order melalui form admin:

```php
// Cek stok sebelum membuat order
foreach ($validated['items'] as $item) {
    $menu = Menu::find($item['menu_id']);
    if (!$menu->hasEnoughStock($item['quantity'])) {
        return error; // Stok tidak cukup
    }
}
```

---

## Alur Transaksi dengan Sistem Stok

```
1. Cashier Buka Kasir
   ↓
2. Pilih Menu + Jumlah
   ├─ Sistem cek stok?
   ├─ Stok cukup ✓ → Tambah ke keranjang
   └─ Stok kurang ✗ → Error message
   ↓
3. Tambah Item Lain (repeat)
   ↓
4. Click Checkout
   ├─ Final check semua item di keranjang
   ├─ Stok cukup ✓ → Lanjut
   └─ Stok kurang ✗ → Error, batalkan checkout
   ↓
5. Pilih Metode Pembayaran & Submit
   ↓
6. Order Selesai
   ├─ Kurangi stok untuk setiap item
   ├─ Tambah sold_quantity
   └─ Hapus keranjang
   ↓
7. Stok Menu Terupdate di Database
```

---

## Mengelola Stok Menu

### Update Stok via Database

```sql
-- Update stok menu dengan ID 1
UPDATE menus SET stock = 50 WHERE id = 1;

-- Tambah stok (restocking)
UPDATE menus SET stock = stock + 30 WHERE name = 'Nasi Kuning';

-- Reset stok
UPDATE menus SET stock = 0 WHERE id = 5;
```

### Update Stok via Laravel Admin (Jika ada)

Tambahkan field di form edit menu:

```blade
<input type="number" name="stock" value="{{ $menu->stock }}" min="0">
```

---

## Contoh Response API

### ✅ Tambah ke Keranjang - Sukses

```json
{
  "success": true,
  "message": "Nasi Kuning ditambahkan ke keranjang",
  "cart": {...},
  "cart_count": 3
}
```

### ❌ Tambah ke Keranjang - Stok Kurang

```json
{
    "success": false,
    "message": "Stok Nasi Kuning tidak cukup. Stok tersedia: 5, diminta total: 8"
}
```

### ❌ Checkout - Stok Kurang

```json
{
    "success": false,
    "message": "Stok Lumpia tidak cukup. Stok tersedia: 2"
}
```

---

## Testing Fitur Stok

### Test Case 1: Stok Cukup

1. Menu stok = 10
2. Tambah 5 unit → ✅ Sukses
3. Checkout → ✅ Stok jadi 5

### Test Case 2: Stok Kurang

1. Menu stok = 3
2. Coba tambah 5 unit → ❌ Error
3. Tambah 3 unit → ✅ Sukses

### Test Case 3: Multiple Items

1. Menu A stok = 5, Menu B stok = 3
2. Keranjang: A (3 unit), B (2 unit)
3. Checkout → ✅ Stok A = 2, B = 1

---

## File yang Diubah

| File                                                                 | Perubahan                                                     |
| -------------------------------------------------------------------- | ------------------------------------------------------------- |
| `database/migrations/2024_12_02_000001_add_stock_to_menus_table.php` | ✨ Baru - Tambah kolom stock                                  |
| `app/Models/Menu.php`                                                | 📝 Update - Tambah 'stock' di fillable, tambah helper methods |
| `app/Http/Controllers/CashierController.php`                         | 📝 Update - Validasi stok di addToCart() dan checkout()       |
| `app/Http/Controllers/OrderController.php`                           | 📝 Update - Validasi stok di store()                          |
| `resources/views/cashier/index.blade.php`                            | 📝 Update - Tampilkan badge stok, disable button jika habis   |
| `database/seeders/AddStockToMenusSeeder.php`                         | ✨ Baru - Set default stok = 10 untuk menu                    |

---

## Catatan Penting

⚠️ **Stok tidak boleh negatif** - Sistem telah dijaga dengan validasi di controller
⚠️ **Sinkronisasi** - Stok dan sold_quantity terupdate bersamaan
⚠️ **Performance** - Untuk data besar, pertimbangkan query optimization

---

## Support

Jika ada issue atau pertanyaan, hubungi tim development!
