<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Menu;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        // Total Revenue
        $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('total_amount');

        // Total Orders
        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();

        // Average Sale
        $averageSale = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Total Discount (simplified)
        $totalDiscount = 0;

        // Payment Methods Distribution
        $paymentMethods = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('payment_method')
            ->get();

        // Popular Menu Items
        $popularMenus = Menu::orderBy('sold_quantity', 'desc')->take(5)->get();

        // Sales data for chart (last 30 days)
        $salesByDate = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('dashboard.index', compact(
            'totalRevenue',
            'totalOrders',
            'averageSale',
            'totalDiscount',
            'paymentMethods',
            'popularMenus',
            'salesByDate'
        ));
    }

    public function getChartData()
    {
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        $salesByDate = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Jika tidak ada data, generate dummy data untuk 30 hari terakhir
        if ($salesByDate->isEmpty()) {
            $labels = [];
            $data = [];
            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $labels[] = $date->format('M d');
                $data[] = 0;
            }
        } else {
            $labels = $salesByDate->pluck('date')->map(fn($d) => date('M d', strtotime($d)))->toArray();
            $data = $salesByDate->pluck('total')->toArray();
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    public function getPaymentData()
    {
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        $paymentMethods = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('payment_method')
            ->get();

        // Initialize dengan 0 untuk semua metode
        $methods = [
            'cash' => 0, 
            'credit_card' => 0, 
            'qris' => 0
        ];
        
        foreach ($paymentMethods as $payment) {
            if (isset($methods[$payment->payment_method])) {
                $methods[$payment->payment_method] = (int)$payment->count;
            }
        }

        return response()->json([
            'labels' => ['Tunai', 'Kartu Kredit/Debit', 'QRIS'],
            'data' => [
                $methods['cash'],
                $methods['credit_card'],
                $methods['qris']
            ],
        ]);
    }
}
