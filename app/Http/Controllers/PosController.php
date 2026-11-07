<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $categories      = Category::where('is_active', true)->get();
        $storeName       = Setting::get('store_name', 'POS PRO INDONESIA');
        $qrisImagePath   = Setting::get('qris_image_path', '');
        $receiptPaperSize = Setting::get('receipt_paper_size', '80mm');
        $receiptAutoPrint = Setting::get('receipt_auto_print', '1');

        return view('pos.index', compact(
            'categories',
            'storeName',
            'qrisImagePath',
            'receiptPaperSize',
            'receiptAutoPrint'
        ));
    }

    public function getProducts(Request $request)
    {
        $query = Product::where('is_active', true);

        if ($request->filled('category_id') && $request->category_id !== 'all') {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name', 'asc')->get();

        return response()->json($products);
    }

    /**
     * Integrasi Midtrans Snap: Generate Snap Token dari Keranjang Belanja Kasir
     */
    public function getMidtransToken(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'discount' => 'nullable|numeric|min:0',
            'customer_name' => 'nullable|string|max:100',
        ]);

        try {
            // Validasi ketersediaan stok produk
            foreach ($request->items as $item) {
                $product = Product::find($item['id']);
                if (!$product) {
                    throw new \Exception('Produk tidak ditemukan.');
                }
                if ($product->stock < $item['qty']) {
                    throw new \Exception("Stok {$product->name} tidak mencukupi (sisa: {$product->stock}).");
                }
            }

            $discount = floatval($request->discount ?? 0);
            $customerName = $request->customer_name ?: 'Pelanggan Umum';

            $result = MidtransService::createSnapTransaction(
                $request->items,
                $discount,
                $customerName
            );

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memproses Midtrans Snap: ' . $e->getMessage()
            ], 422);
        }
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'payment_method' => 'required|in:qris,cash',
            'customer_name' => 'nullable|string|max:100',
            'discount' => 'nullable|numeric|min:0',
            'amount_paid' => 'required|numeric|min:0',
            'payment_reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $subtotal = 0;
            $itemsData = [];

            // Verify items & calculate total and profit
            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->find($item['id']);

                if (!$product) {
                    throw new \Exception('Produk tidak ditemukan.');
                }

                if ($product->stock < $item['qty']) {
                    throw new \Exception("Stok {$product->name} tidak mencukupi (sisa: {$product->stock}).");
                }

                $itemSubtotal = $product->selling_price * $item['qty'];
                $itemProfit = ($product->selling_price - $product->cost_price) * $item['qty'];
                $subtotal += $itemSubtotal;

                // Deduct stock
                $product->decrement('stock', $item['qty']);

                $itemsData[] = [
                    'product_id' => $product->id,
                    'product_code' => $product->code,
                    'product_name' => $product->name,
                    'cost_price' => $product->cost_price,
                    'selling_price' => $product->selling_price,
                    'quantity' => $item['qty'],
                    'subtotal' => $itemSubtotal,
                    'profit' => $itemProfit,
                ];
            }

            $discount = floatval($request->discount ?? 0);
            $totalAmount = max(0, $subtotal - $discount);
            $amountPaid = floatval($request->amount_paid);

            if ($amountPaid < $totalAmount) {
                throw new \Exception('Jumlah pembayaran kurang dari total tagihan.');
            }

            $changeAmount = $amountPaid - $totalAmount;

            // Generate Invoice Number: INV-YYYYMMDD-XXXX
            $today = Carbon::now()->format('Ymd');
            $countToday = Transaction::whereDate('created_at', Carbon::today())->count() + 1;
            $invoiceNumber = 'INV-' . $today . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            // Reference code based on gateway
            $paymentRef = $request->payment_reference;
            if (!$paymentRef) {
                if ($request->payment_method === 'qris') {
                    $paymentRef = 'QRIS-' . date('YmdHis') . rand(100, 999);
                } else {
                    $paymentRef = 'CASH-' . date('His');
                }
            }

            $transaction = Transaction::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => Auth::id(),
                'customer_name' => $request->customer_name ?: 'Pelanggan Umum',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => 0,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'payment_status' => 'paid',
                'payment_reference' => $paymentRef,
                'amount_paid' => $amountPaid,
                'change_amount' => $changeAmount,
                'notes' => $request->notes,
            ]);

            foreach ($itemsData as $item) {
                $item['transaction_id'] = $transaction->id;
                TransactionItem::create($item);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi BERHASIL diselesaikan!',
                'transaction' => $transaction->load(['items', 'user']),
                'receipt_url' => route('pos.receipt', $transaction->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Transaksi GAGAL: ' . $e->getMessage()
            ], 422);
        }
    }

    public function printReceipt($id, Request $request)
    {
        $transaction = Transaction::with(['items', 'user'])->findOrFail($id);

        // Security check for cashier
        if (Auth::user()->isCashier() && $transaction->user_id !== Auth::id()) {
            abort(403, 'Akses Ditolak: Anda tidak berhak melihat struk transaksi kasir lain.');
        }

        $paperSize = $request->query('paper_size', Setting::get('receipt_paper_size', '57mm'));
        if (!in_array($paperSize, ['57mm', '30mm'])) {
            $paperSize = '57mm';
        }

        $autoPrint = $request->query('autoprint', '1') === '1';

        $storeName = Setting::get('store_name', 'POS PRO INDONESIA');
        $storeAddress = Setting::get('store_address', 'Jl. Sudirman Mega Mall Lt. 1 No. 45, Jakarta');
        $storePhone = Setting::get('store_phone', '0812-9988-7766');
        $storeFooter = Setting::get('store_footer', 'Terima kasih atas kunjungan Anda!');

        return view('pos.receipt', compact(
            'transaction',
            'storeName',
            'storeAddress',
            'storePhone',
            'storeFooter',
            'paperSize',
            'autoPrint'
        ));
    }

    public function history(Request $request)
    {
        $query = Transaction::with(['user', 'items']);

        // Kasir can only see their own transactions if not admin
        if (Auth::user()->isCashier()) {
            $query->where('user_id', Auth::id());
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('payment_reference', 'like', "%{$search}%");
            });
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        $transactions = $query->latest()->paginate(5)->withQueryString();

        $totalSales = (clone $query)->where('payment_status', 'paid')->sum('total_amount');
        $totalTransactions = (clone $query)->count();

        return view('pos.history', compact('transactions', 'totalSales', 'totalTransactions'));
    }

    public function destroyHistory(Request $request, $id)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Hanya administrator yang dapat menghapus riwayat transaksi.');
        }

        try {
            $transaction = Transaction::find($id);

            if (!$transaction) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Transaksi tidak ditemukan atau sudah dihapus.'
                    ], 404);
                }

                return redirect()->route('pos.history')
                    ->with('swal_error', 'Transaksi tidak ditemukan atau sudah dihapus.');
            }

            $invoice = $transaction->invoice_number;

            $transaction->items()->delete();
            $transaction->delete();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Transaksi <strong>' . e($invoice) . '</strong> BERHASIL dihapus dari riwayat.'
                ], 200);
            }

            return redirect()->route('pos.history')
                ->with('swal_success', 'Transaksi "' . $invoice . '" BERHASIL dihapus!');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'GAGAL menghapus transaksi! Error: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('pos.history')
                ->with('swal_error', 'GAGAL menghapus transaksi! Error: ' . $e->getMessage());
        }
    }

    // Integrasi Cetak ke Printer: Laporan Penjualan (Web Print View A4 dengan auto window.print)
    public function printSales(Request $request)
    {
        $query = Transaction::with(['user', 'items']);

        // Kasir can only see their own transactions if not admin
        if (Auth::user()->isCashier()) {
            $query->where('user_id', Auth::id());
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
            $periodLabel = Carbon::parse($request->start_date)->translatedFormat('d M Y') . ' s/d ' . Carbon::parse($request->end_date)->translatedFormat('d M Y');
        } else {
            $periodLabel = 'Keseluruhan Total Penjualan (Semua Periode)';
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('payment_reference', 'like', "%{$search}%");
            });
        }

        $transactions = $query->latest()->get();

        $totalOmset = $transactions->where('payment_status', 'paid')->sum('total_amount');
        $totalItemsSold = $transactions->flatMap->items->sum('quantity');
        $totalTransactions = $transactions->count();
        $totalProfit = $transactions->sum(function ($tx) {
            return $tx->items->sum('profit');
        });

        $byPaymentMethod = $transactions->groupBy('payment_method')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total_amount')
            ];
        });

        $storeName = Setting::get('store_name', 'POS PRO INDONESIA');
        $storeAddress = Setting::get('store_address', 'Jl. Sudirman Mega Mall Lt. 1 No. 45, Jakarta');
        $storePhone = Setting::get('store_phone', '0812-9988-7766');

        return view('reports.sales-print', compact(
            'transactions',
            'totalOmset',
            'totalItemsSold',
            'totalTransactions',
            'totalProfit',
            'byPaymentMethod',
            'periodLabel',
            'storeName',
            'storeAddress',
            'storePhone'
        ));
    }

    // Fitur Kasir 3: Cetak PDF Keseluruhan Total Penjualan (Download atau Stream untuk Print)
    public function exportSalesPdf(Request $request)
    {
        $query = Transaction::with(['user', 'items']);

        // Kasir can only see their own transactions if not admin
        if (Auth::user()->isCashier()) {
            $query->where('user_id', Auth::id());
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
            $periodLabel = Carbon::parse($request->start_date)->translatedFormat('d M Y') . ' s/d ' . Carbon::parse($request->end_date)->translatedFormat('d M Y');
        } else {
            $periodLabel = 'Keseluruhan Total Penjualan (Semua Periode)';
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $transactions = $query->latest()->get();

        $totalOmset = $transactions->where('payment_status', 'paid')->sum('total_amount');
        $totalItemsSold = $transactions->flatMap->items->sum('quantity');
        $totalTransactions = $transactions->count();
        $totalProfit = $transactions->sum(function ($tx) {
            return $tx->items->sum('profit');
        });

        $byPaymentMethod = $transactions->groupBy('payment_method')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total_amount')
            ];
        });

        $storeName = Setting::get('store_name', 'POS PRO INDONESIA');
        $storeAddress = Setting::get('store_address', 'Jakarta');

        $pdf = Pdf::loadView('reports.sales-pdf', compact(
            'transactions',
            'totalOmset',
            'totalItemsSold',
            'totalTransactions',
            'totalProfit',
            'byPaymentMethod',
            'periodLabel',
            'storeName',
            'storeAddress'
        ))->setPaper('a4', 'portrait');

        if ($request->query('action') === 'print' || $request->query('stream') == 1) {
            return $pdf->stream('Laporan_Penjualan_' . date('Ymd_His') . '.pdf');
        }

        return $pdf->download('Laporan_Penjualan_' . date('Ymd_His') . '.pdf');
    }

    /**
     * Webhook Notifikasi HTTP Midtrans (Menerima status pembayaran otomatis dari Midtrans)
     */
    public function midtransNotification(Request $request)
    {
        $payload = $request->all();
        $orderId = $payload['order_id'] ?? null;
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = (string) ($payload['signature_key'] ?? '');
        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus = $payload['fraud_status'] ?? 'accept';

        if (!$orderId) {
            return response()->json(['message' => 'Order ID tidak ditemukan'], 400);
        }

        // Verifikasi keaslian webhook signature Midtrans jika server key diset
        $serverKey = MidtransService::getServerKey();
        if ($serverKey && !str_starts_with($serverKey, 'SB-Mid-server-TEST')) {
            $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
            if (!hash_equals($expectedSignature, $signatureKey)) {
                return response()->json(['message' => 'Invalid signature key'], 403);
            }
        }

        // Cari transaksi berdasarkan order_id / payment_reference atau nomor invoice
        $transaction = Transaction::where('payment_reference', $orderId)
            ->orWhere('invoice_number', $orderId)
            ->first();

        if ($transaction) {
            if ($transactionStatus === 'capture') {
                if ($fraudStatus === 'accept') {
                    $transaction->update(['payment_status' => 'paid']);
                }
            } elseif ($transactionStatus === 'settlement') {
                $transaction->update(['payment_status' => 'paid']);
            } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                $transaction->update(['payment_status' => 'cancelled']);
            } elseif ($transactionStatus === 'pending') {
                $transaction->update(['payment_status' => 'pending']);
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Notifikasi Midtrans berhasil diproses']);
    }
}
