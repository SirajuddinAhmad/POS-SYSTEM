<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Total Produk
        $totalProducts = Product::count();
        $lowStockProductsCount = Product::where('stock', '<=', DB::raw('min_stock'))->count();

        // 2. Total Penjualan (Revenue of paid transactions)
        $totalSalesAmount = Transaction::where('payment_status', 'paid')->sum('total_amount');
        $totalTransactionsCount = Transaction::where('payment_status', 'paid')->count();

        // Today's Sales
        $todaySalesAmount = Transaction::where('payment_status', 'paid')
            ->whereDate('created_at', Carbon::today())
            ->sum('total_amount');

        // 3. Total Laba Kotor (Gross Profit = sum(profit) in paid transaction items)
        $totalGrossProfit = TransactionItem::whereHas('transaction', function ($q) {
            $q->where('payment_status', 'paid');
        })->sum('profit');

        // 4. Traffic Penjualan (Last 7 days data for chart)
        $salesTraffic = [];
        $salesTrafficLabels = [];
        $salesTrafficValues = [];
        $salesTrafficProfits = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateString = $date->format('Y-m-d');
            $displayDate = $date->translatedFormat('d M');

            $daySales = Transaction::where('payment_status', 'paid')
                ->whereDate('created_at', $dateString)
                ->sum('total_amount');

            $dayProfit = TransactionItem::whereHas('transaction', function ($q) use ($dateString) {
                $q->where('payment_status', 'paid')
                  ->whereDate('created_at', $dateString);
            })->sum('profit');

            $salesTrafficLabels[] = $displayDate;
            $salesTrafficValues[] = (float) $daySales;
            $salesTrafficProfits[] = (float) $dayProfit;
        }

        // 5. Grafik Top Trending Produk (Top 5 products by quantity sold)
        $topProducts = TransactionItem::select(
                'product_name',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_revenue'),
                DB::raw('SUM(profit) as total_profit')
            )
            ->whereHas('transaction', function ($q) {
                $q->where('payment_status', 'paid');
            })
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $topProductLabels = $topProducts->pluck('product_name')->toArray();
        $topProductQuantities = $topProducts->pluck('total_qty')->map(fn($v) => (int)$v)->toArray();
        $topProductRevenues = $topProducts->pluck('total_revenue')->map(fn($v) => (float)$v)->toArray();

        // Recent 5 Transactions
        $recentTransactions = Transaction::with(['user', 'items'])
            ->latest()
            ->limit(5)
            ->get();

        // Low stock products list
        $lowStockProducts = Product::with('category')
            ->where('stock', '<=', DB::raw('min_stock'))
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'lowStockProductsCount',
            'totalSalesAmount',
            'totalTransactionsCount',
            'todaySalesAmount',
            'totalGrossProfit',
            'salesTrafficLabels',
            'salesTrafficValues',
            'salesTrafficProfits',
            'topProductLabels',
            'topProductQuantities',
            'topProductRevenues',
            'topProducts',
            'recentTransactions',
            'lowStockProducts'
        ));
    }
}
