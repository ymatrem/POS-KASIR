@extends('layouts.app')

@section('title', 'Tambah Pesanan')
@section('page-title', 'Tambah Pesanan Baru')
@section('page-subtitle')
    <p class="text-sm text-gray-500">Buat pesanan baru</p>
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
            @csrf

            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="payment_method" class="block text-sm font-semibold text-gray-700 mb-2">Metode Pembayaran *</label>
                    <select id="payment_method" name="payment_method" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                        <option value="">Pilih Metode Pembayaran</option>
                        <option value="cash" @selected(old('payment_method') === 'cash')>Tunai</option>
                        <option value="credit_card" @selected(old('payment_method') === 'credit_card')>Kartu Kredit/Debit</option>
                        <option value="qris" @selected(old('payment_method') === 'qris')>QRIS</option>
                    </select>
                </div>
            </div>

            <h3 class="text-lg font-semibold text-gray-800 mb-4">Item Pesanan</h3>

            <div id="itemsContainer" class="mb-6 space-y-4">
                <div class="itemRow grid grid-cols-5 gap-4 bg-gray-50 p-4 rounded-lg">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Menu</label>
                        <select name="items[0][menu_id]" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                            <option value="">Pilih Menu</option>
                            @foreach($menus as $menu)
                                <option value="{{ $menu->id }}" data-price="{{ $menu->price }}">{{ $menu->name }} - Rp {{ number_format($menu->price, 0, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Jumlah</label>
                        <input type="number" name="items[0][quantity]" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500" min="1" value="1" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Harga</label>
                        <input type="hidden" name="items[0][price]" class="itemPrice" value="0">
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100" disabled>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Diskon %</label>
                        <input type="number" name="items[0][discount]" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500" min="0" value="0">
                    </div>
                    <div class="flex items-end">
                        <button type="button" class="removeItem bg-red-500 text-white px-3 py-2 rounded-lg hover:bg-red-600 transition w-full">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>

            <button type="button" id="addItem" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition mb-6">
                <i class="fas fa-plus mr-2"></i> Tambah Item
            </button>

            <div class="border-t pt-6">
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Total Jumlah</p>
                        <p class="text-2xl font-bold text-gray-800">
                            <span id="totalQuantity">0</span>
                        </p>
                        <input type="hidden" id="totalQuantityInput" name="total_quantity" value="0">
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Diskon</p>
                        <p class="text-2xl font-bold text-gray-800">
                            Rp <span id="totalDiscountAmount">0</span>
                        </p>
                    </div>
                    <div class="bg-orange-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Total Harga</p>
                        <p class="text-2xl font-bold text-gray-800">
                            Rp <span id="totalAmount">0</span>
                        </p>
                        <input type="hidden" id="totalAmountInput" name="total_amount" value="0">
                    </div>
                </div>
            </div>

            <div class="flex space-x-4 mt-6">
                <button type="submit" class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition">
                    <i class="fas fa-save mr-2"></i> Simpan Pesanan
                </button>
                <a href="{{ route('orders.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    let itemCount = 1;

    function updateCalculations() {
        let totalQty = 0;
        let totalDiscount = 0;
        let totalAmt = 0;

        document.querySelectorAll('.itemRow').forEach((row, index) => {
            const menuSelect = row.querySelector('select[name*="menu_id"]');
            const qtyInput = row.querySelector('input[name*="quantity"]');
            const priceInput = row.querySelector('.itemPrice');
            const discountInput = row.querySelector('input[name*="discount"]');
            const priceDisplay = row.querySelector('input[disabled]');

            const selectedOption = menuSelect.selectedOptions[0];
            const price = selectedOption ? parseFloat(selectedOption.dataset.price) : 0;
            const qty = parseInt(qtyInput.value) || 0;
            const discount = parseFloat(discountInput.value) || 0;

            priceInput.value = price;
            priceDisplay.value = 'Rp ' + new Intl.NumberFormat('id-ID').format(price);

            const itemTotal = price * qty;
            const discountAmount = (itemTotal * discount) / 100;
            const itemFinal = itemTotal - discountAmount;

            totalQty += qty;
            totalDiscount += discountAmount;
            totalAmt += itemFinal;
        });

        document.getElementById('totalQuantity').textContent = totalQty;
        document.getElementById('totalQuantityInput').value = totalQty;
        document.getElementById('totalDiscountAmount').textContent = new Intl.NumberFormat('id-ID').format(totalDiscount);
        document.getElementById('totalAmount').textContent = new Intl.NumberFormat('id-ID').format(totalAmt);
        document.getElementById('totalAmountInput').value = totalAmt;
    }

    document.getElementById('addItem').addEventListener('click', function() {
        const container = document.getElementById('itemsContainer');
        const newRow = document.querySelector('.itemRow').cloneNode(true);

        newRow.querySelectorAll('input, select').forEach(el => {
            const name = el.getAttribute('name');
            if (name) {
                el.setAttribute('name', name.replace(/\[\d+\]/, `[${itemCount}]`));
                el.value = '';
            }
        });

        newRow.querySelector('input[name*="quantity"]').value = '1';
        newRow.querySelector('input[name*="discount"]').value = '0';

        container.appendChild(newRow);
        attachItemListeners(newRow);
        itemCount++;
    });

    function attachItemListeners(row) {
        row.querySelector('select').addEventListener('change', updateCalculations);
        row.querySelector('input[name*="quantity"]').addEventListener('input', updateCalculations);
        row.querySelector('input[name*="discount"]').addEventListener('input', updateCalculations);
        row.querySelector('.removeItem').addEventListener('click', function() {
            if (document.querySelectorAll('.itemRow').length > 1) {
                row.remove();
                updateCalculations();
            } else {
                alert('Minimal harus ada 1 item order');
            }
        });
    }

    document.querySelectorAll('.itemRow').forEach(row => attachItemListeners(row));
    updateCalculations();
</script>
@endpush
