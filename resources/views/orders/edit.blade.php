@extends('layouts.app')

@section('title', 'Edit Pesanan')
@section('page-title', 'Edit Pesanan')
@section('page-subtitle')
    <p class="text-sm text-gray-500">Perbarui informasi pesanan</p>
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('orders.update', $order->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-3 gap-6 mb-6">
                <div>
                    <label for="order_number" class="block text-sm font-semibold text-gray-700 mb-2">Nomor Pesanan</label>
                    <input type="text" id="order_number" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100" value="{{ $order->order_number }}" disabled>
                </div>

                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status *</label>
                    <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                        <option value="pending" @selected(old('status', $order->status) === 'pending')>Menunggu</option>
                        <option value="completed" @selected(old('status', $order->status) === 'completed')>Selesai</option>
                        <option value="cancelled" @selected(old('status', $order->status) === 'cancelled')>Dibatalkan</option>
                    </select>
                </div>

                <div>
                    <label for="payment_method" class="block text-sm font-semibold text-gray-700 mb-2">Metode Pembayaran *</label>
                    <select id="payment_method" name="payment_method" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                        <option value="cash" @selected(old('payment_method', $order->payment_method) === 'cash')>Tunai</option>
                        <option value="credit_card" @selected(old('payment_method', $order->payment_method) === 'credit_card')>Kartu Kredit/Debit</option>
                        <option value="qris" @selected(old('payment_method', $order->payment_method) === 'qris')>QRIS</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label for="total_amount" class="block text-sm font-semibold text-gray-700 mb-2">Total Harga (Rp) *</label>
                <input type="number" id="total_amount" name="total_amount" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" value="{{ old('total_amount', $order->total_amount) }}" required>
            </div>

            <h3 class="text-lg font-semibold text-gray-800 mb-4">Item Pesanan</h3>

            <div class="bg-gray-50 rounded-lg p-4 mb-6 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2 px-2">Menu</th>
                            <th class="text-left py-2 px-2">Jumlah</th>
                            <th class="text-left py-2 px-2">Harga</th>
                            <th class="text-left py-2 px-2">Diskon</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->items as $item)
                            <tr class="border-b">
                                <td class="py-2 px-2">{{ $item->menu->name }}</td>
                                <td class="py-2 px-2">{{ $item->quantity }}</td>
                                <td class="py-2 px-2">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="py-2 px-2">Rp {{ number_format($item->discount, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-gray-500">No items</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex space-x-4">
                <button type="submit" class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition">
                    <i class="fas fa-save mr-2"></i> Perbarui Pesanan
                </button>
                <a href="{{ route('orders.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
