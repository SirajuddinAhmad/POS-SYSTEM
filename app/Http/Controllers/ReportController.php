<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Expense;
use App\Models\Setting;
use App\Exports\ProfitLossExport;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    protected function getProfitLossData(Request $request)
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfMonth()->startOfDay();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfMonth()->endOfDay();

        // 1. Penjualan & HPP
        $transactions = Transaction::with('items')
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalRevenue = $transactions->sum('total_amount');
        $totalDiscount = $transactions->sum('discount');
        $grossSales = $transactions->sum('subtotal');

        // Total HPP (Cost of Goods Sold)
        $totalHpp = $transactions->flatMap->items->sum(function ($item) {
            return $item->cost_price * $item->quantity;
        });

        // Total Laba Kotor (Gross Profit)
        $totalGrossProfit = $grossSales - $totalHpp - $totalDiscount;

        // 2. Beban Operasional (Expenses)
        $expenses = Expense::with('user')
            ->whereBetween('expense_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('expense_date', 'asc')
            ->get();

        $totalExpenses = $expenses->sum('amount');
        $expensesByCategory = $expenses->groupBy('category')->map(function ($group) {
            return $group->sum('amount');
        });

        // 3. Total Laba Bersih (Net Profit/Loss)
        $totalNetProfit = $totalGrossProfit - $totalExpenses;
        $isLoss = $totalNetProfit < 0;

        $storeName = Setting::get('store_name', 'POS PRO INDONESIA');
        $storeAddress = Setting::get('store_address', 'Jakarta');

        $periodLabel = $startDate->translatedFormat('d M Y') . ' s/d ' . $endDate->translatedFormat('d M Y');

        return compact(
            'startDate',
            'endDate',
            'periodLabel',
            'grossSales',
            'totalDiscount',
            'totalRevenue',
            'totalHpp',
            'totalGrossProfit',
            'expenses',
            'totalExpenses',
            'expensesByCategory',
            'totalNetProfit',
            'isLoss',
            'storeName',
            'storeAddress'
        );
    }

    public function profitLoss(Request $request)
    {
        $data = $this->getProfitLossData($request);
        return view('reports.profit-loss', $data);
    }

    // Integrasi Cetak ke Printer: Laporan Laba Bersih & Rugi (A4 Print Layout)
    public function printProfitLoss(Request $request)
    {
        $data = $this->getProfitLossData($request);
        $data['storePhone'] = Setting::get('store_phone', '0812-9988-7766');
        return view('reports.profit-loss-print', $data);
    }

    // Fitur 1: Cetak PDF Total Keseluruhan Laba Bersih & Rugi (Download atau Stream untuk Print)
    public function exportProfitLossPdf(Request $request)
    {
        $data = $this->getProfitLossData($request);

        $pdf = Pdf::loadView('reports.profit-loss-pdf', $data)
            ->setPaper('a4', 'portrait');

        if ($request->query('action') === 'print' || $request->query('stream') == 1) {
            return $pdf->stream('Laporan_Laba_Rugi_' . date('Ymd_His') . '.pdf');
        }

        return $pdf->download('Laporan_Laba_Rugi_' . date('Ymd_His') . '.pdf');
    }

    // Fitur 2: Export ke Excel Laba Bersih & Rugi
    public function exportProfitLossExcel(Request $request)
    {
        $data = $this->getProfitLossData($request);

        return Excel::download(
            new ProfitLossExport($data),
            'Laporan_Laba_Rugi_' . date('Ymd_His') . '.xlsx'
        );
    }
}
