@extends('layouts.app')

@section('title', 'Kasir')
@section('page-title', 'Sistem Kasir')
@section('page-subtitle')
    <p class="text-sm text-gray-500">Proses penjualan dan checkout</p>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Menu List (Left Side) -->
        <div class="lg:col-span-2">
            <!-- Category Filter -->
            <div class="mb-6 flex gap-2 overflow-x-auto pb-2">
                <button class="category-filter px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition flex-shrink-0" data-category="all">
                    Semua
                </button>
                @foreach($categories as $category)
                    <button class="category-filter px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition flex-shrink-0" data-category="{{ $category->id }}">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>

            <!-- Menu Grid -->
            <div class="grid grid-cols-5 gap-3">
                @foreach($menus as $menu)
                    <div class="menu-card bg-white rounded-lg shadow hover:shadow-lg transition cursor-pointer" data-menu-id="{{ $menu->id }}" data-category="{{ $menu->category_id }}">
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

                            <!-- Price and Add Button -->
                            <div class="flex flex-col gap-1">
                                <p class="text-sm font-bold text-orange-600">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                                <button class="add-to-cart-btn bg-blue-500 text-white px-2 py-1 rounded-lg hover:bg-blue-600 transition text-xs w-full" data-menu-id="{{ $menu->id }}" data-menu-name="{{ $menu->name }}">
                                    <i class="fas fa-shopping-cart mr-1"></i> Tambah
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Shopping Cart (Right Side) -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 sticky top-24">
                <h2 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-shopping-cart mr-2 text-orange-500"></i> Keranjang
                </h2>

                <!-- Cart Items -->
                <div id="cartContainer" class="space-y-3 mb-6 max-h-96 overflow-y-auto">
                    <div id="emptyCart" class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 block text-gray-300"></i>
                        <p>Keranjang kosong</p>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="border-t pt-4 mb-4">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-semibold">Rp <span id="subtotal">0</span></span>
                    </div>
                    <div class="flex justify-between mb-3">
                        <span class="text-gray-600">Diskon (%):</span>
                        <input type="number" id="discountInput" min="0" max="100" value="0" class="w-20 px-2 py-1 border border-gray-300 rounded text-right">
                    </div>
                    <div class="flex justify-between mb-4 bg-orange-50 p-3 rounded-lg">
                        <span class="font-semibold text-gray-800">Total:</span>
                        <span class="font-bold text-orange-600 text-lg">Rp <span id="totalAmount">0</span></span>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Metode Pembayaran</label>
                    <select id="paymentMethod" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="cash">Tunai</option>
                        <option value="credit_card">Kartu Kredit/Debit</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-2">
                    <button id="checkoutBtn" class="w-full bg-green-500 text-white px-4 py-3 rounded-lg hover:bg-green-600 transition font-semibold" disabled>
                        <i class="fas fa-check-circle mr-2"></i> Checkout
                    </button>
                    <button id="clearCartBtn" class="w-full bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition text-sm">
                        <i class="fas fa-trash mr-2"></i> Kosongkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk input kuantitas -->
    <div id="quantityModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-80">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Masukkan Jumlah</h3>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah:</label>
                <input type="number" id="quantityInput" min="1" value="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
            <div class="flex gap-2">
                <button id="confirmQuantityBtn" class="flex-1 bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition font-semibold">
                    Tambahkan
                </button>
                <button id="cancelQuantityBtn" class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Success -->
    <div id="successModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-8 w-96 text-center">
            <div class="mb-4">
                <i class="fas fa-check-circle text-green-500 text-6xl block mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Checkout Berhasil!</h3>
            </div>
            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Nomor Pesanan:</p>
                <p class="text-xl font-bold text-orange-600" id="orderNumber">INV-XXXXXXXX</p>
            </div>
            <div class="flex gap-2">
                <button id="printReceiptBtn" class="flex-1 bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition font-semibold">
                    <i class="fas fa-print mr-2"></i> Cetak Struk
                </button>
                <button id="closeSuccessBtn" class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
                    Selesai
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let currentMenuId = null;
    let currentOrderId = null;

    // Add to cart button click
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            currentMenuId = this.dataset.menuId;
            document.getElementById('quantityInput').value = '1';
            document.getElementById('quantityModal').classList.remove('hidden');
        });
    });

    // Category filter
    document.querySelectorAll('.category-filter').forEach(btn => {
        btn.addEventListener('click', function() {
            const selectedCategory = this.dataset.category;
            
            // Update button styles
            document.querySelectorAll('.category-filter').forEach(b => b.classList.remove('bg-orange-500', 'text-white'));
            document.querySelectorAll('.category-filter').forEach(b => b.classList.add('bg-white', 'text-gray-700', 'border', 'border-gray-300'));
            this.classList.remove('bg-white', 'text-gray-700', 'border', 'border-gray-300');
            this.classList.add('bg-orange-500', 'text-white');

            // Filter menu cards
            document.querySelectorAll('.menu-card').forEach(card => {
                if (selectedCategory === 'all' || card.dataset.category === selectedCategory) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // Confirm quantity
    document.getElementById('confirmQuantityBtn').addEventListener('click', function() {
        const quantity = parseInt(document.getElementById('quantityInput').value);
        
        if (quantity > 0) {
            fetch('{{ route("cashier.add-to-cart") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    menu_id: currentMenuId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateCart();
                    showNotification(data.message, 'success');
                }
                document.getElementById('quantityModal').classList.add('hidden');
            })
            .catch(error => console.error('Error:', error));
        }
    });

    // Cancel quantity modal
    document.getElementById('cancelQuantityBtn').addEventListener('click', function() {
        document.getElementById('quantityModal').classList.add('hidden');
    });

    // Update cart display
    function updateCart() {
        fetch('{{ route("cashier.get-cart") }}')
            .then(response => response.json())
            .then(data => {
                const cartContainer = document.getElementById('cartContainer');
                const cart = data.cart;

                if (Object.keys(cart).length === 0) {
                    cartContainer.innerHTML = `
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2 block text-gray-300"></i>
                            <p>Keranjang kosong</p>
                        </div>
                    `;
                    document.getElementById('checkoutBtn').disabled = true;
                    document.getElementById('subtotal').textContent = '0';
                    document.getElementById('totalAmount').textContent = '0';
                } else {
                    cartContainer.innerHTML = Object.entries(cart).map(([menuId, item]) => `
                        <div class="bg-gray-50 p-3 rounded-lg flex justify-between items-center">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800 text-sm">${item.name}</p>
                                <p class="text-xs text-gray-500">${item.quantity} x Rp ${parseInt(item.price).toLocaleString('id-ID')}</p>
                                <p class="text-sm font-semibold text-orange-600">Rp ${(item.price * item.quantity).toLocaleString('id-ID')}</p>
                            </div>
                            <button class="remove-from-cart-btn text-red-500 hover:text-red-700 ml-2" data-menu-id="${menuId}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `).join('');

                    document.getElementById('checkoutBtn').disabled = false;

                    // Attach remove event listeners
                    document.querySelectorAll('.remove-from-cart-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            removeFromCart(this.dataset.menuId);
                        });
                    });

                    // Update totals
                    const discount = parseInt(document.getElementById('discountInput').value) || 0;
                    updateTotals(data.total, discount);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Remove from cart
    function removeFromCart(menuId) {
        fetch(`/cashier/remove-from-cart/${menuId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCart();
                showNotification(data.message, 'info');
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Update totals
    function updateTotals(subtotal, discount) {
        const discountAmount = (subtotal * discount) / 100;
        const total = subtotal - discountAmount;
        
        document.getElementById('subtotal').textContent = subtotal.toLocaleString('id-ID');
        document.getElementById('totalAmount').textContent = total.toLocaleString('id-ID');
    }

    // Discount input change
    document.getElementById('discountInput').addEventListener('change', function() {
        fetch('{{ route("cashier.get-cart") }}')
            .then(response => response.json())
            .then(data => {
                const discount = parseInt(this.value) || 0;
                updateTotals(data.total, discount);
            })
            .catch(error => console.error('Error:', error));
    });

    // Checkout
    document.getElementById('checkoutBtn').addEventListener('click', function() {
        const paymentMethod = document.getElementById('paymentMethod').value;
        const discount = parseInt(document.getElementById('discountInput').value) || 0;

        fetch('{{ route("cashier.checkout") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                payment_method: paymentMethod,
                discount: discount
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentOrderId = data.order.id;
                document.getElementById('orderNumber').textContent = data.order_number;
                document.getElementById('successModal').classList.remove('hidden');
                document.getElementById('discountInput').value = '0';
                updateCart();
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan', 'error');
        });
    });

    // Clear cart
    document.getElementById('clearCartBtn').addEventListener('click', function() {
        if (confirm('Yakin ingin mengosongkan keranjang?')) {
            fetch('{{ route("cashier.clear-cart") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateCart();
                    showNotification(data.message, 'info');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });

    // Print receipt
    document.getElementById('printReceiptBtn').addEventListener('click', function() {
        window.open(`/cashier/print-receipt/${currentOrderId}`, '_blank');
    });

    // Close success modal
    document.getElementById('closeSuccessBtn').addEventListener('click', function() {
        document.getElementById('successModal').classList.add('hidden');
    });

    // Show notification
    function showNotification(message, type = 'info') {
        const bgColor = type === 'success' ? 'bg-green-100 text-green-700 border-green-400' :
                       type === 'error' ? 'bg-red-100 text-red-700 border-red-400' :
                       'bg-blue-100 text-blue-700 border-blue-400';

        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-4 py-3 rounded border ${bgColor} z-40 animate-fade-in`;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => notification.remove(), 3000);
    }

    // Initialize cart on page load
    updateCart();
</script>
@endpush
