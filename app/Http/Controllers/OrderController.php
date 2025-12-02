<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items')->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $menus = Menu::all();
        return view('orders.create', compact('menus'));
    }

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

        // Validasi stok untuk semua item
        foreach ($validated['items'] as $item) {
            $menu = Menu::find($item['menu_id']);

            if (!$menu->hasEnoughStock($item['quantity'])) {
                return back()->withErrors([
                    'stock_error' => "Stok {$menu->name} tidak cukup. Stok tersedia: {$menu->stock}, diminta: {$item['quantity']}"
                ])->withInput();
            }
        }

        // Jika semua stok cukup, buat order
        $order = Order::create([
            'order_number' => 'ORD-' . time(),
            'total_amount' => $validated['total_amount'],
            'total_quantity' => $validated['total_quantity'],
            'payment_method' => $validated['payment_method'],
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        foreach ($validated['items'] as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item['menu_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'discount' => $item['discount'] ?? 0,
            ]);

            $menu = Menu::find($item['menu_id']);
            $menu->increment('sold_quantity', $item['quantity']);
            // Kurangi stok
            $menu->decreaseStock($item['quantity']);
        }

        return redirect()->route('orders.index')->with('success', 'Order berhasil dibuat');
    }

    public function edit(Order $order)
    {
        $menus = Menu::all();
        return view('orders.edit', compact('order', 'menus'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'total_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,credit_card,qris',
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        $order->update($validated);
        return redirect()->route('orders.index')->with('success', 'Order berhasil diperbarui');
    }

    public function destroy(Order $order)
    {
        $order->items()->delete();
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order berhasil dihapus');
    }
}
