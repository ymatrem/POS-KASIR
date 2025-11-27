<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $order->order_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .receipt {
                width: 80mm;
                margin: 0;
                padding: 0;
            }
        }
        .receipt {
            font-family: 'Courier New', monospace;
            max-width: 80mm;
            margin: 0 auto;
            padding: 10mm;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="receipt bg-white p-4">
        <!-- Header -->
        <div class="text-center border-b pb-4 mb-4">
            <h1 class="text-2xl font-bold">TOKO POS</h1>
            <p class="text-sm text-gray-600">Sistem Kasir</p>
        </div>

        <!-- Invoice Info -->
        <div class="text-center mb-4 border-b pb-4">
            <p class="font-bold">{{ $order->order_number }}</p>
            <p class="text-xs text-gray-600">{{ $order->created_at->format('d/m/Y H:i:s') }}</p>
        </div>

        <!-- Items -->
        <div class="mb-4 border-b pb-4">
            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-1">Item</th>
                        <th class="text-center py-1">Qty</th>
                        <th class="text-right py-1">Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr class="border-b">
                            <td class="py-1">{{ $item->menu->name }}</td>
                            <td class="text-center py-1">{{ $item->quantity }}</td>
                            <td class="text-right py-1">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                        </tr>
                        @if($item->discount > 0)
                            <tr class="text-xs text-gray-600">
                                <td colspan="3" class="text-right py-1">Diskon: -Rp {{ number_format($item->discount, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="mb-4 border-b pb-4 space-y-1 text-xs">
            <div class="flex justify-between">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($order->items->sum(function($item) { return $item->price * $item->quantity; }), 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Total Diskon:</span>
                <span>-Rp {{ number_format($order->items->sum('discount'), 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between font-bold text-base mt-2 pt-2 border-t">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="text-center mb-4 border-b pb-4 text-xs">
            <p class="font-bold">{{ ucfirst(str_replace('_', '/', $order->payment_method)) }}</p>
            <p class="text-gray-600">Status: {{ ucfirst($order->status) }}</p>
        </div>

        <!-- Footer -->
        <div class="text-center text-xs text-gray-600">
            <p>Terima kasih telah berbelanja!</p>
            <p class="mt-2">{{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>

    <div class="text-center mt-8">
        <button onclick="window.print()" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">
            <i class="fas fa-print"></i> Cetak
        </button>
        <button onclick="window.close()" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition ml-2">
            Tutup
        </button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></script>
    <script>
        // Auto print on load
        // window.print();
    </script>
</body>
</html>
