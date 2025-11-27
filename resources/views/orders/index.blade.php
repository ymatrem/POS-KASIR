@extends('layouts.app')

@section('title', 'Pesanan')
@section('page-title', 'Pesanan')
@section('page-subtitle')
    <p class="text-sm text-gray-500">Kelola semua pesanan</p>
@endsection

@section('content')
    <div class="mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Daftar Pesanan</h2>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-700">No</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-700">Nomor Pesanan</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-700">Total Harga</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-700">Jumlah</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-700">Metode Pembayaran</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-700">Status</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-700">Tanggal</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($orders as $index => $order)
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 px-6 text-sm">{{ ($orders->currentPage() - 1) * $orders->perPage() + $index + 1 }}</td>
                            <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $order->order_number }}</td>
                            <td class="py-4 px-6 text-sm font-semibold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td class="py-4 px-6 text-sm">{{ $order->total_quantity }}</td>
                            <td class="py-4 px-6 text-sm">
                                @if($order->payment_method === 'cash')
                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Tunai</span>
                                @elseif($order->payment_method === 'credit_card')
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">Kartu Kredit/Debit</span>
                                @elseif($order->payment_method === 'qris')
                                    <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-semibold">QRIS</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-sm">
                                @if($order->status === 'completed')
                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Selesai</span>
                                @elseif($order->status === 'pending')
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">Menunggu</span>
                                @elseif($order->status === 'cancelled')
                                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">Dibatalkan</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-sm">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="py-4 px-6 text-sm">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('orders.edit', $order->id) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('orders.destroy', $order->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin hapus?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500">Belum ada pesanan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t">
            {{ $orders->links('pagination::tailwind') }}
        </div>
    </div>
@endsection
