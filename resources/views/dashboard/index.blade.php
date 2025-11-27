@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle')
    <p class="text-sm text-gray-500">Berikut adalah detail analitik toko Anda</p>
@endsection

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-2">Total Pendapatan</p>
                    <p class="text-3xl font-bold text-gray-800">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                    <p class="text-sm text-green-600 mt-2">
                        <i class="fas fa-arrow-up"></i> 10.5% Dari bulan lalu
                    </p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-coins text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Order -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-2">Total Pesanan</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalOrders }}</p>
                    <p class="text-sm text-green-600 mt-2">
                        <i class="fas fa-arrow-up"></i> 1.2% Dari bulan lalu
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-bar text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Average Sale -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-2">Rata-rata Penjualan</p>
                    <p class="text-3xl font-bold text-gray-800">Rp {{ number_format($averageSale, 0, ',', '.') }}</p>
                    <p class="text-sm text-green-600 mt-2">
                        <i class="fas fa-arrow-up"></i> 3.5% Dari bulan lalu
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Discount -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-2">Total Diskon</p>
                    <p class="text-3xl font-bold text-gray-800">Rp {{ number_format($totalDiscount, 0, ',', '.') }}</p>
                    <p class="text-sm text-red-600 mt-2">
                        <i class="fas fa-arrow-down"></i> 2.8% Dari bulan lalu
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tag text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Popular Menu -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Payment Method Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Metode Pembayaran</h3>
            </div>
            <canvas id="paymentChart" height="100"></canvas>
            <div class="mt-4 space-y-2 text-sm">
                <div class="flex items-center justify-between">
                    <span class="flex items-center">
                        <span class="inline-block w-3 h-3 bg-purple-500 rounded-full mr-2"></span>
                        Tunai
                    </span>
                    <span id="cashCount">-</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center">
                        <span class="inline-block w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                        Kartu Kredit/Debit
                    </span>
                    <span id="cardCount">-</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center">
                        <span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                        QRIS
                    </span>
                    <span id="qrisCount">-</span>
                </div>
            </div>
        </div>

        <!-- Popular Menu -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Menu Terpopuler</h3>
                <input type="text" placeholder="Cari menu..." class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600">No</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600">Menu</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600">Harga</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600">Terjual</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600">Diskon</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600">Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($popularMenus as $index => $menu)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-4 text-sm">{{ $index + 1 }}</td>
                                <td class="py-3 px-4 text-sm">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden">
                                            @if($menu->image_url)
                                                <img src="{{ $menu->image_url }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                                            @else
                                                <i class="fas fa-image text-gray-400"></i>
                                            @endif
                                        </div>
                                        <span class="font-medium text-gray-800">{{ $menu->name }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-sm">Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-sm">{{ $menu->sold_quantity }}</td>
                                <td class="py-3 px-4 text-sm">10%</td>
                                <td class="py-3 px-4 text-sm font-semibold">Rp {{ number_format($menu->price * $menu->sold_quantity, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-500">Belum ada menu yang terjual</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Payment Chart
    const paymentDataUrl = '{{ url("/api/payment-data") }}';
    fetch(paymentDataUrl)
        .then(response => response.json())
        .then(data => {
            document.getElementById('cashCount').textContent = data.data[0];
            document.getElementById('cardCount').textContent = data.data[1];
            document.getElementById('qrisCount').textContent = data.data[2];

            const ctx = document.getElementById('paymentChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.data,
                        backgroundColor: [
                            '#a855f7',
                            '#3b82f6',
                            '#22c55e'
                        ],
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        });
</script>
@endpush
