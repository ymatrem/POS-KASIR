# 📋 USE CASE DIAGRAM - POS KASIR (Mermaid Code)

Buka di: https://mermaid.live dan paste code dibawah ini 👇

---

## 🎯 CODE MERMAID - USE CASE DIAGRAM

```mermaid
graph TD
    System["🖥️ POS Kasir<br/>(Cashier Module)"]

    Cashier["👤 Cashier<br/>(Actor)"]

    UC1["UC1: View<br/>Available Menus<br/>━━━━━━━━━━━━<br/>• Display all menus<br/>• Show stock<br/>• Show price"]

    UC2["UC2: Filter Menus<br/>by Category<br/>━━━━━━━━━━━━<br/>• Filter by cat<br/>• Show filtered"]

    UC3["UC3: View Menu<br/>Details<br/>━━━━━━━━━━━━<br/>• Name<br/>• Price<br/>• Stock Available<br/>• Category<br/>• Description"]

    UC4["UC4: Add Item<br/>to Cart<br/>━━━━━━━━━━━━<br/>• Select menu item<br/>• Enter quantity<br/>• Validate stock<br/>• Add to session<br/>• Show success"]

    UC5["UC5: Validate Stock<br/>━━━━━━━━━━━━<br/>• Check menu stock<br/>• Compare with qty<br/>• Return true/false"]

    UC6["UC6: Update Cart<br/>Item<br/>━━━━━━━━━━━━<br/>• Select item<br/>• Change qty<br/>• Re-validate<br/>• Update session<br/>• Update total"]

    UC7["UC7: Remove Item<br/>from Cart<br/>━━━━━━━━━━━━<br/>• Select item<br/>• Confirm removal<br/>• Remove from cart<br/>• Update total"]

    UC8["UC8: View Cart<br/>Summary<br/>━━━━━━━━━━━━<br/>• Display all items<br/>• Show qty & price<br/>• Calculate subtotal<br/>• Show total<br/>• Checkout button"]

    UC9["UC9: Checkout<br/>━━━━━━━━━━━━<br/>• Select payment<br/>• Enter discount<br/>• Final validation<br/>• Calculate amount<br/>• Create order<br/>• Decrease stock<br/>• Clear cart"]

    UC10["UC10: Print Receipt<br/>━━━━━━━━━━━━<br/>• Get order details<br/>• Format receipt<br/>• Print/Display<br/>• Show order #<br/>• Show items<br/>• Show total"]

    ERROR1["❌ Error:<br/>Stock Insufficient<br/>━━━━━━━━━━━━<br/>• Show error msg<br/>• Qty exceeds stock"]

    ERROR2["❌ Error:<br/>Cart Empty"]

    ERROR3["❌ Error:<br/>Invalid Input<br/>━━━━━━━━━━━━<br/>• Invalid payment<br/>• Invalid discount"]

    %% Actor flows
    Cashier -->|main| UC1
    Cashier -->|main| UC2
    Cashier -->|main| UC3
    Cashier -->|main| UC4
    Cashier -->|main| UC6
    Cashier -->|main| UC7
    Cashier -->|main| UC8
    Cashier -->|main| UC9
    Cashier -->|main| UC10

    %% View flows
    UC1 -->|filter| UC2
    UC1 -->|show detail| UC3
    UC3 -->|leads to| UC4

    %% Include relationships (dotted)
    UC4 -.->|<<include>>| UC5
    UC6 -.->|<<include>>| UC5
    UC9 -.->|<<include>>| UC5

    %% Extend relationships (error)
    UC4 -.->|<<extend>>| ERROR1
    UC6 -.->|<<extend>>| ERROR1
    UC9 -.->|<<extend>>| ERROR1
    UC9 -.->|<<extend>>| ERROR2
    UC4 -.->|<<extend>>| ERROR3

    %% Main flow
    UC4 -->|next| UC8
    UC6 -->|update| UC8
    UC7 -->|remove| UC8
    UC8 -->|proceed| UC9
    UC9 -->|success| UC10

    %% Styling
    style System fill:#E8F4F8,stroke:#0066cc,stroke-width:3px
    style Cashier fill:#FFE4B5,stroke:#ff6600,stroke-width:2px
    style UC5 fill:#FFB6C6,stroke:#cc0000,stroke-width:2px
    style ERROR1 fill:#FFB6C6,stroke:#cc0000,stroke-width:2px
    style ERROR2 fill:#FFB6C6,stroke:#cc0000,stroke-width:2px
    style ERROR3 fill:#FFB6C6,stroke:#cc0000,stroke-width:2px
```

---

## 🚀 CARA PAKAI:

### **Step 1: Copy seluruh code diatas (mulai dari ` ```mermaid ` sampai ` ``` `)**

### **Step 2: Buka https://mermaid.live**

### **Step 3: Paste di area kode sebelah kiri**

### **Step 4: Diagram otomatis muncul di sebelah kanan!**

### **Step 5: Download PNG (klik tombol Export → PNG)**

---

## 📊 ALTERNATIF - Format Diagram Klasik (Lebih Mirip Original)

```mermaid
graph TD
    System["<b>System: POS Kasir</b><br/>(Cashier Module)"]
    Cashier["<b>Cashier</b><br/>(Actor)"]

    subgraph View ["📋 VIEW PHASE"]
        UC1["UC1: View Menus"]
        UC2["UC2: Filter by Category"]
        UC3["UC3: View Menu Details"]
    end

    subgraph Cart ["🛒 CART PHASE"]
        UC4["UC4: Add to Cart"]
        UC6["UC6: Update Cart"]
        UC7["UC7: Remove from Cart"]
        UC8["UC8: View Cart"]
    end

    subgraph Checkout ["💳 CHECKOUT PHASE"]
        UC9["UC9: Checkout"]
        UC10["UC10: Print Receipt"]
    end

    Validation["✅ UC5: Validate Stock"]
    E1["❌ Error: Stock Insufficient"]
    E2["❌ Error: Cart Empty"]
    E3["❌ Error: Invalid Input"]

    %% Connections
    Cashier --> UC1
    Cashier --> UC2
    Cashier --> UC3

    UC1 --> UC4
    UC2 --> UC1
    UC3 --> UC4

    UC4 --> UC8
    UC6 --> UC8
    UC7 --> UC8

    UC8 --> UC9
    UC9 --> UC10

    UC4 -.->|include| Validation
    UC6 -.->|include| Validation
    UC9 -.->|include| Validation

    UC4 -.->|extend| E1
    UC6 -.->|extend| E1
    UC9 -.->|extend| E1
    UC9 -.->|extend| E2
    UC4 -.->|extend| E3

    style View fill:#e3f2fd
    style Cart fill:#f3e5f5
    style Checkout fill:#fce4ec
    style Validation fill:#ffcccc
    style E1 fill:#ffcccc
    style E2 fill:#ffcccc
    style E3 fill:#ffcccc
```

---

## 📋 DAFTAR USE CASE

| No  | Use Case                    | Deskripsi                                    |
| --- | --------------------------- | -------------------------------------------- |
| 1   | **UC1: View Menus**         | Cashier melihat semua menu tersedia          |
| 2   | **UC2: Filter by Category** | Cashier filter menu berdasarkan kategori     |
| 3   | **UC3: View Menu Details**  | Cashier lihat detail menu (harga, stok, dll) |
| 4   | **UC4: Add to Cart**        | Cashier tambah item ke keranjang             |
| 5   | **UC5: Validate Stock**     | System validasi ketersediaan stok ⭐         |
| 6   | **UC6: Update Cart**        | Cashier ubah quantity item di cart           |
| 7   | **UC7: Remove from Cart**   | Cashier hapus item dari cart                 |
| 8   | **UC8: View Cart**          | Cashier lihat ringkasan keranjang            |
| 9   | **UC9: Checkout**           | Cashier lakukan pembayaran & buat order      |
| 10  | **UC10: Print Receipt**     | Cashier cetak bukti transaksi                |

---

## 🔄 ALUR TRANSAKSI LENGKAP

```
👤 Cashier
    ↓
📋 UC1: View Menus (semua produk ditampilkan)
    ↓
🏷️ UC2: Filter by Category (opsional - filter kategori)
    ↓
📝 UC3: View Menu Details (lihat detail produk)
    ↓
🛒 UC4: Add to Cart + ✅ UC5: Validate Stock
    ├─ Jika stok tidak cukup → ❌ ERROR: Stock Insufficient
    └─ Jika OK → item masuk keranjang
    ↓
[Repeat: UC4 (add), UC6 (update qty), UC7 (remove)]
    ↓
👁️ UC8: View Cart (lihat total belanja)
    ↓
💳 UC9: Checkout
    ├─ Pilih payment method
    ├─ Enter discount
    ├─ Final validation: ✅ UC5: Validate Stock untuk SEMUA item
    ├─ Jika ada error → ❌ Error: Stock/Payment/Discount Invalid
    └─ Jika OK → Create Order + Decrease Stock
    ↓
📄 UC10: Print Receipt
    ↓
✅ END OF TRANSACTION
```

---

## 💡 TIPS

**Untuk hasil terbaik di mermaid.live:**

1. **Copy seluruh code** (jangan ada yang ketinggalan)
2. **Paste di sisi kiri** mermaid.live
3. **Tunggu 2 detik** untuk render
4. **Klik Export** → pilih PNG/SVG
5. **Download** gambar

---

**Generated:** 2 Desember 2025
**Format:** Mermaid Diagram (Universal)
**Kompatibel:** GitHub, GitLab, Notion, Confluence, docs semua
**Status:** ✅ Ready to use
