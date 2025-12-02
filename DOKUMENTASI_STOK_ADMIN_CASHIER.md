✅ SISTEM STOK DASHBOARD ADMIN - DOKUMENTASI LENGKAP

═══════════════════════════════════════════════════════════════════
🎯 FITUR YANG TELAH DITAMBAHKAN:
═══════════════════════════════════════════════════════════════════

1. ✅ DASHBOARD ADMIN - INPUT & MANAGE STOK
   └─ Tambah Menu: Ada input field untuk stok
   └─ Edit Menu: Bisa update stok di halaman edit
   └─ List Menu: Tampilkan stok dengan color badge
   ├─ 🟢 Hijau: Stok cukup (> 5 unit)
   ├─ 🟡 Kuning: Stok terbatas (1-5 unit)
   └─ 🔴 Merah: Stok habis (0 unit)

2. ✅ CASHIER - VALIDASI STOK
   └─ Tidak bisa transaksi melebihi stok admin
   └─ Check stok saat tambah ke keranjang
   └─ Check stok saat checkout
   └─ Auto kurangi stok saat transaksi selesai

═══════════════════════════════════════════════════════════════════
📋 FILES YANG DIMODIFIKASI:
═══════════════════════════════════════════════════════════════════

BACKEND:
├─ app/Http/Controllers/MenuController.php
│ ├─ store() → Tambah validasi 'stock' field
│ └─ update() → Tambah validasi 'stock' field
│
└─ app/Models/Menu.php (sudah selesai sebelumnya)
├─ hasEnoughStock($quantity)
   ├─ decreaseStock($quantity)
└─ increaseStock($quantity)

FRONTEND - VIEWS:
├─ resources/views/menus/create.blade.php
│ └─ Tambah input field: "Stok"
│
├─ resources/views/menus/edit.blade.php
│ └─ Tambah input field: "Stok"
│ └─ Show current stock value
│
└─ resources/views/menus/index.blade.php
└─ Tampilkan stock status dengan color badge
├─ Status label (Cukup/Terbatas/Habis)
└─ Stock counter (📦 Stok: X)

═══════════════════════════════════════════════════════════════════
🔄 ALUR KERJA - ADMIN & CASHIER:
═══════════════════════════════════════════════════════════════════

STEP 1: ADMIN SETUP STOK
┌─ Admin buka Menu Management
├─ Klik "Tambah Menu Baru"
├─ Isi:
│ ├─ Nama Menu: "Nasi Kuning"
│ ├─ Harga: 12000
│ ├─ Kategori: Makanan
│ ├─ Stok: 50 ← INPUT BARU!
│ └─ Gambar: (upload)
├─ Klik "Simpan Menu"
└─ Menu tersimpan dengan stok = 50

STEP 2: ADMIN LIHAT DAFTAR MENU
┌─ Admin ke halaman "Data Master"
├─ Lihat list menu dalam card view
├─ Setiap card menampilkan:
│ ├─ Gambar menu
│ ├─ Nama & Kategori
│ ├─ Harga
│ ├─ Terjual (sold_quantity)
│ └─ 📦 Stok: 50 (dengan color badge)
└─ Color sesuai kondisi:
├─ 🟢 Hijau jika stok > 5
├─ 🟡 Kuning jika stok 1-5 (⚠️ Terbatas)
└─ 🔴 Merah jika stok 0 (⚠️ Habis)

STEP 3: ADMIN UPDATE STOK
┌─ Admin ke halaman "Data Master"
├─ Lihat menu "Nasi Kuning" (Stok: 50, 🟢 Hijau)
├─ Klik tombol "Ubah"
├─ Halaman edit terbuka
├─ Ubah stok: 50 → 30 (misalnya restocking)
├─ Klik "Perbarui Menu"
└─ Stok terupdate: 30

STEP 4: CASHIER TRANSAKSI NORMAL
┌─ Cashier buka halaman Kasir
├─ Lihat menu "Nasi Kuning" dengan badge "Stok: 30"
├─ Klik menu tersebut
├─ Input qty: 5 unit
├─ Sistem cek: 5 <= 30? ✓ YES
├─ Tambah ke keranjang ✓
├─ Checkout
├─ Payment method: Tunai
├─ Submit
├─ Order created
├─ Stok berkurang: 30 - 5 = 25
└─ Database menus: stok = 25

STEP 5: CASHIER TRANSAKSI - STOK KURANG
┌─ Stok saat ini: 25
├─ Cashier coba ambil: 30 unit
├─ Sistem cek: 30 <= 25? ✗ NO
├─ Error message: "Stok tidak cukup. Tersedia: 25"
├─ Tidak bisa tambah ke keranjang
├─ Stok tetap: 25
└─ Cashier coba ambil: 25 unit ✓ OK

STEP 6: REAL-TIME SYNC
┌─ Admin lihat menu "Nasi Kuning"
├─ Stok awalnya: 25
├─ Cashier checkout 10 unit
├─ Database update: stok = 15
├─ Admin refresh halaman
├─ Lihat stok updated: 15
└─ Sync real-time bekerja!

═══════════════════════════════════════════════════════════════════
🎯 VALIDASI LOGIC - CASHIER:
═══════════════════════════════════════════════════════════════════

Location: CashierController.php

1️⃣ addToCart() - Validasi Tambah ke Keranjang

Input: menu_id, quantity

Logic:
├─ Get menu from database
├─ Check: Menu stok >= quantity?
│ ├─ YES ✓ → Tambah ke cart
│ └─ NO ✗ → Error 422 (Stok tidak cukup)
│
├─ If item sudah di cart:
│ ├─ newQty = cartQty + requestQty
│ ├─ Check: Menu stok >= newQty?
│ │ ├─ YES ✓ → Update cart
│ │ └─ NO ✗ → Error 422
│  
 └─ Database stok: TIDAK BERKURANG (masih di cart)

2️⃣ checkout() - Validasi & Proses Checkout

Input: payment_method, discount

Logic:
├─ For each item in cart:
│ ├─ Get menu from database
│ └─ Check: Menu stok >= item qty?
│ ├─ YES ✓ → Continue
│ └─ NO ✗ → Error 422, stop checkout
│
├─ If all items OK:
│ ├─ Create Order
│ ├─ For each item:
│ │ ├─ Create OrderItem
│ │ ├─ Update sold_quantity++
│ │ └─ DECREASE STOCK by quantity ← PENTING!
│ │
│ ├─ Clear cart
│ └─ Return success
│
└─ Database stok: BERKURANG (final)

═══════════════════════════════════════════════════════════════════
📊 CONTOH DATA FLOW:
═══════════════════════════════════════════════════════════════════

SKENARIO 1: Transaksi Normal
┌─ Menu: Nasi Kuning
├─ DB Stok: 50
├─ Cashier ambil: 15 unit
├─ Validation: 15 <= 50? YES ✓
├─ Order created
├─ Stok berkurang: 50 - 15 = 35
├─ sold_quantity: + 15
└─ DB Stok: 35 ✓

SKENARIO 2: Stok Kurang - Add Cart Failed
┌─ Menu: Lumpia
├─ DB Stok: 5
├─ Cashier coba: 10 unit
├─ Validation: 10 <= 5? NO ✗
├─ Error: "Stok Lumpia tidak cukup. Tersedia: 5"
├─ Tidak bisa tambah ke cart
├─ DB Stok: 5 (tidak berubah)
└─ Cashier coba lagi: 5 unit ✓

SKENARIO 3: Stok Habis
┌─ Menu: Kopi
├─ DB Stok: 0
├─ Cashier coba: 1 unit
├─ Validation: 1 <= 0? NO ✗
├─ Error: "Stok Kopi tidak cukup. Tersedia: 0"
├─ Badge di cashier: 🔴 Habis (button disabled)
└─ Tidak bisa transaksi

SKENARIO 4: Multiple Items - Partial Success
┌─ Cart:
│ ├─ Item A (stok 10): ambil 5 ✓
│ └─ Item B (stok 2): ambil 3 ✗
├─ Validation: B stok kurang
├─ Error: "Stok Item B tidak cukup"
├─ Checkout FAILED
├─ Cart masih ada (bisa edit qty)
└─ DB Stok: Tidak berubah sama sekali

═══════════════════════════════════════════════════════════════════
🎨 UI COMPONENTS:
═══════════════════════════════════════════════════════════════════

ADMIN - MENU FORM (Create/Edit):
┌─────────────────────────────────────────┐
│ Nama Menu _ │
│ ┌────────────────────────────────────┐ │
│ │ Nasi Kuning │ │
│ └────────────────────────────────────┘ │
│ │
│ Deskripsi │
│ ┌────────────────────────────────────┐ │
│ │ Makanan tradisional... │ │
│ └────────────────────────────────────┘ │
│ │
│ Harga (Rp) _ │ Stok _ │
│ ┌──────────────┐ │ ┌──────────────┐ │
│ │ 12000 │ │ │ 50 │ │
│ └──────────────┘ │ └──────────────┘ │
│ │
│ Kategori _ │
│ ┌────────────────────────────────────┐ │
│ │ Makanan ▼ │ │
│ └────────────────────────────────────┘ │
│ │
│ [Simpan Menu] [Batal] │
└─────────────────────────────────────────┘

ADMIN - MENU CARD (List View):
┌──────────────────────┐
│ [Menu Image] │
├──────────────────────┤
│ Nasi Kuning │
│ Makanan │
│ Rp 12.000 │
│ Terjual: 25 │
├──────────────────────┤
│ 🟢 Stok: 50 │ ← Green badge
└──────────────────────┘

CASHIER - PRODUCT CARD:
┌────────────────────┐
│ [Menu Image] │
│ 🟢 Stok: 50 ← │ Badge
├────────────────────┤
│ Nasi Kuning │
│ Rp 12.000 │
│ │
│ [Tambah] │ ← Active
└────────────────────┘

CASHIER - PRODUCT HABIS:
┌────────────────────┐
│ [Menu Image] │
│ 🔴 Stok: 0 ← │ Red badge
├────────────────────┤
│ Kopi │
│ Rp 5.000 │
│ │
│ [Habis] │ ← Disabled
└────────────────────┘

═══════════════════════════════════════════════════════════════════
⚙️ DATABASE STRUCTURE:
═══════════════════════════════════════════════════════════════════

TABLE: menus
┌─────────┬──────────────┬────────────────────────────┐
│ Field │ Type │ Notes │
├─────────┼──────────────┼────────────────────────────┤
│ id │ INT PRIMARY │ │
│ name │ VARCHAR(255) │ │
│ price │ DECIMAL(10,2)│ │
│ stock │ INT │ NEW - Default 0 │
│ category│ FK │ │
│ image │ VARCHAR │ │
│ desc │ TEXT │ │
│ sold_qty│ INT │ Increment with stock -- │
│ created │ TIMESTAMP │ │
│ updated │ TIMESTAMP │ │
└─────────┴──────────────┴────────────────────────────┘

═══════════════════════════════════════════════════════════════════
✅ TESTING CHECKLIST:
═══════════════════════════════════════════════════════════════════

ADMIN SIDE:
□ Buka form Tambah Menu
□ Lihat field "Stok" (new)
□ Isi semua field + stok
□ Klik Simpan
□ Menu tersimpan dengan stok
□ Buka halaman List Menu
□ Lihat stok di card
□ Lihat color badge (hijau/kuning/merah)
□ Klik Edit menu
□ Lihat field stok terisi
□ Update stok
□ Klik Perbarui
□ Stok updated di list

CASHIER SIDE:
□ Buka halaman Kasir
□ Lihat badge stok di produk
□ Lihat button status (Tambah/Habis)
□ Tambah produk dengan stok cukup
□ Lihat error jika stok kurang
□ Edit qty di cart
□ Lihat error jika melebihi stok
□ Checkout dengan stok cukup
□ Lihat stok berkurang di database
□ Buka kasir lagi
□ Lihat stok sudah update

DATABASE:
□ Check tabel menus punya kolom stock
□ Check stock value terupdate setelah transaksi

═══════════════════════════════════════════════════════════════════
🔗 RELASI SYNC ADMIN-CASHIER:
═══════════════════════════════════════════════════════════════════

1. Admin set stok di menu → Database update
2. Database store latest stok value
3. Cashier baca stok dari database (real-time)
4. Cashier validate transaksi dengan stok DB
5. Transaksi berhasil → Stok berkurang
6. Admin buka ulang → Lihat stok updated

Flow: Admin Setup → DB Store → Cashier Read → Validate & Transaksi → DB Update

═══════════════════════════════════════════════════════════════════
📞 SUPPORT & TROUBLESHOOTING:
═══════════════════════════════════════════════════════════════════

Q: Stok tidak tampil di form create?
A: Database sudah migration, form sudah di-update
Refresh halaman atau restart server

Q: Cashier bisa transaksi > stok?
A: Tidak! Sudah ada validasi di CashierController
Check error console jika ada issue

Q: Stok tidak berkurang setelah transaksi?
A: Check di database (PHP MyAdmin)
Atau cek order_items apakah tersimpan

Q: Badge warna tidak sesuai?
A: Warna logic:

-   > 5 = Hijau (Cukup)
-   1-5 = Kuning (Terbatas)
-   0 = Merah (Habis)

═══════════════════════════════════════════════════════════════════
✅ SELESAI!
═══════════════════════════════════════════════════════════════════

Sistem stok sudah fully integrated:
✓ Admin bisa manage stok
✓ Cashier tidak bisa transaksi > stok
✓ Real-time sync antara admin & cashier
✓ Color badge menampilkan status stok
✓ Database terupdate otomatis

Ready for production! 🚀
