<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CashierController extends Controller
{
    /**
     * Display the cashier interface with available menus
     */
    public function index()
    {
        $menus = Menu::with('category')->get();
        $categories = Menu::distinct('category_id')->pluck('category_id')->map(function ($id) {
            return Menu::where('category_id', $id)->first()?->category;
        })->filter()->values();
        
        return view('cashier.index', compact('menus', 'categories'));
    }

    /**
     * Add item to cart (session-based)
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $menu = Menu::findOrFail($request->menu_id);
        $cart = session()->get('cashier_cart', []);

        // Check if item already in cart
        if (isset($cart[$menu->id])) {
            $cart[$menu->id]['quantity'] += $request->quantity;
        } else {
            $cart[$menu->id] = [
                'id' => $menu->id,
                'name' => $menu->name,
                'price' => $menu->price,
                'quantity' => $request->quantity,
                'image_url' => $menu->image_url,
                'category' => $menu->category->name ?? 'N/A',
            ];
        }

        session()->put('cashier_cart', $cart);

        return response()->json([
            'success' => true,
            'message' => "{$menu->name} ditambahkan ke keranjang",
            'cart' => $cart,
            'cart_count' => count($cart),
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function updateCart(Request $request, $menuId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cashier_cart', []);

        if (isset($cart[$menuId])) {
            $cart[$menuId]['quantity'] = $request->quantity;
            session()->put('cashier_cart', $cart);

            return response()->json([
                'success' => true,
                'message' => 'Keranjang diperbarui',
                'cart' => $cart,
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Item tidak ditemukan'], 404);
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart($menuId)
    {
        $cart = session()->get('cashier_cart', []);

        if (isset($cart[$menuId])) {
            $itemName = $cart[$menuId]['name'];
            unset($cart[$menuId]);
            session()->put('cashier_cart', $cart);

            return response()->json([
                'success' => true,
                'message' => "{$itemName} dihapus dari keranjang",
                'cart' => $cart,
                'cart_count' => count($cart),
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Item tidak ditemukan'], 404);
    }

    /**
     * Get current cart
     */
    public function getCart()
    {
        $cart = session()->get('cashier_cart', []);
        $total = 0;
        $quantity = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
            $quantity += $item['quantity'];
        }

        return response()->json([
            'cart' => $cart,
            'total' => $total,
            'quantity' => $quantity,
            'items_count' => count($cart),
        ]);
    }

    /**
     * Process checkout and create order
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,credit_card,qris',
            'discount' => 'nullable|numeric|min:0|max:100',
        ]);

        $cart = session()->get('cashier_cart', []);

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang kosong',
            ], 400);
        }

        try {
            // Calculate totals
            $totalAmount = 0;
            $totalQuantity = 0;
            $discountPercentage = $request->discount ?? 0;

            foreach ($cart as $item) {
                $itemTotal = $item['price'] * $item['quantity'];
                $totalAmount += $itemTotal;
                $totalQuantity += $item['quantity'];
            }

            // Apply discount
            $discountAmount = ($totalAmount * $discountPercentage) / 100;
            $finalAmount = $totalAmount - $discountAmount;

            // Create order
            $order = Order::create([
                'order_number' => 'INV-' . Str::upper(Str::random(8)) . '-' . now()->format('YmdHis'),
                'total_amount' => $finalAmount,
                'total_quantity' => $totalQuantity,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            // Create order items
            foreach ($cart as $item) {
                $discountPerItem = ($item['price'] * $item['quantity'] * $discountPercentage) / 100;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'discount' => $discountPerItem,
                ]);

                // Update menu sold quantity
                Menu::find($item['id'])->increment('sold_quantity', $item['quantity']);
            }

            // Clear cart
            session()->forget('cashier_cart');

            return response()->json([
                'success' => true,
                'message' => 'Checkout berhasil!',
                'order' => $order,
                'order_number' => $order->order_number,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Checkout gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear cart
     */
    public function clearCart()
    {
        session()->forget('cashier_cart');

        return response()->json([
            'success' => true,
            'message' => 'Keranjang dikosongkan',
        ]);
    }

    /**
     * Print receipt (view)
     */
    public function printReceipt($orderId)
    {
        $order = Order::with('items.menu')->findOrFail($orderId);
        return view('cashier.receipt', compact('order'));
    }
}
