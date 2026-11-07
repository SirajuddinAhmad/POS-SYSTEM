@extends('layouts.app')

@section('title', 'Laporan Laba Bersih & Rugi')
@section('page_title', 'Laporan Laba Bersih & Rugi')
@section('page_subtitle', 'Analisis komprehensif pendapatan penjualan, harga pokok penjualan (HPP), dan beban operasional')

@section('content')
<div class="container-fluid px-0">

    <!-- Filter & Action Card -->
    <div class="card border-0 shadow-sm rounded-2xl bg-white mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <!-- Date Filter Form -->
                <form action="{{ route('reports.profit-loss') }}" method="GET" class="form-inline gap-2">
                    <label class="text-xs font-bold text-slate-700 mr-2">PERIODE:</label>
                    <input type="date" name="start_date" class="form-control form-control-sm text-sm" value="{{ $startDate->format('Y-m-d') }}">
                    <span class="text-slate-400 mx-1">s/d</span>
                    <input type="date" name="end_date" class="form-control form-control-sm text-sm" value="{{ $endDate->format('Y-m-d') }}">
                    <button type="submit" class="btn btn-primary btn-sm rounded-lg font-bold ml-2 px-3">
                        <i class="fa-solid fa-arrows-rotate mr-1"></i> Terapkan
                    </button>
                </form>

                <!-- Export & Print Buttons -->
                <div class="d-flex flex-wrap gap-2">
                    <!-- Integrasi Cetak ke Printer -->
                    <a href="{{ route('reports.profit-loss.print', request()->query()) }}" target="_blank" class="btn btn-primary btn-sm rounded-xl font-bold px-3 py-2 shadow-sm d-inline-flex align-items-center" title="Buka dialog cetak laporan ke printer (A4)">
                        <i class="fa-solid fa-print mr-1.5 text-base"></i> Cetak ke Printer
                    </a>

                    <!-- Fitur 1: Cetak Pdf Total Keseluruhan -->
                    <a href="{{ route('reports.profit-loss.pdf', request()->query()) }}" class="btn btn-outline-danger btn-sm rounded-xl font-bold px-3 py-2 shadow-sm d-inline-flex align-items-center" title="Download berkas PDF">
                        <i class="fa-solid fa-file-pdf mr-1.5 text-base"></i> Unduh PDF
                    </a>

                    <!-- Fitur 2: Export ke Excel -->
                    <a href="{{ route('reports.profit-loss.excel', request()->query()) }}" class="btn btn-outline-success btn-sm rounded-xl font-bold px-3 py-2 shadow-sm d-inline-flex align-items-center">
                        <i class="fa-solid fa-file-excel mr-1.5 text-base"></i> Export Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Summary Cards: 3. Total Laba Bersih & 4. Total Laba Rugi Summary -->
    <div class="row mb-4">
        <!-- Pendapatan Bersih -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm rounded-2xl bg-white p-4">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Penjualan Bersih</span>
                <h3 class="text-2xl font-black text-slate-800 my-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                <span class="text-xs text-slate-500">Gross: Rp {{ number_format($grossSales, 0, ',', '.') }} (Diskon: Rp {{ number_format($totalDiscount, 0, ',', '.') }})</span>
            </div>
        </div>

        <!-- HPP / Biaya Modal -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm rounded-2xl bg-white p-4">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total HPP (Modal Barang)</span>
                <h3 class="text-2xl font-black text-rose-600 my-1">Rp {{ number_format($totalHpp, 0, ',', '.') }}</h3>
                <span class="text-xs text-slate-500">Harga Pokok Produk Terjual</span>
            </div>
        </div>

        <!-- Laba Kotor -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm rounded-2xl bg-white p-4">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Laba Kotor</span>
                <h3 class="text-2xl font-black text-indigo-600 my-1">Rp {{ number_format($totalGrossProfit, 0, ',', '.') }}</h3>
                <span class="text-xs text-indigo-500 font-semibold">Penjualan Bersih - HPP</span>
            </div>
        </div>

        <!-- 3. & 4. Total Laba Bersih / Rugi Card -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm rounded-2xl {{ $isLoss ? 'bg-rose-50 border-rose-200' : 'bg-emerald-50 border-emerald-200' }} p-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-xs font-bold {{ $isLoss ? 'text-rose-600' : 'text-emerald-700' }} uppercase tracking-wider">
                        3. TOTAL LABA BERSIH
                    </span>
                    <span class="badge {{ $isLoss ? 'badge-danger' : 'badge-success' }} text-xs font-bold px-2 py-0.5">
                        {{ $isLoss ? 'RUGI' : 'SURPLUS LABA' }}
                    </span>
                </div>
                <h3 class="text-2xl font-black {{ $isLoss ? 'text-rose-700' : 'text-emerald-700' }} my-1">
                    Rp {{ number_format($totalNetProfit, 0, ',', '.') }}
                </h3>
                <span class="text-xs {{ $isLoss ? 'text-rose-600' : 'text-emerald-600' }}">
                    Setelah dikurangi Beban Operasional: Rp {{ number_format($totalExpenses, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    <!-- 4. Total Laba Rugi Financial Statement Table -->
    <div class="card border-0 shadow-sm rounded-2xl bg-white mb-4 overflow-hidden">
        <div class="card-header bg-slate-900 text-white p-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="font-bold text-white mb-0">
                    <i class="fa-solid fa-file-invoice-dollar text-blue-400 mr-2"></i> 4. Laporan Laba Rugi (Income Statement)
                </h5>
                <span class="text-xs text-slate-400">Periode: {{ $periodLabel }}</span>
            </div>
            <span class="badge badge-light font-bold text-slate-800 text-xs px-3 py-1">Standar Akuntansi POS</span>
        </div>

        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0 text-sm">
                <tbody>
                    <!-- Bagian 1: PENDAPATAN OPERASIONAL -->
                    <tr class="bg-slate-50 font-bold text-slate-800 text-xs uppercase tracking-wider">
                        <td colspan="2" class="py-3 px-4">I. PENDAPATAN PENJUALAN</td>
                        <td class="text-right py-3 px-4">JUMLAH (RP)</td>
                    </tr>
                    <tr>
                        <td class="px-5 py-3 text-slate-700">Penjualan Kotor (Subtotal Produk Terjual)</td>
                        <td class="text-right text-slate-500"></td>
                        <td class="text-right font-medium text-slate-800 px-4">Rp {{ number_format($grossSales, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="px-5 py-3 text-slate-700">Potongan & Diskon Penjualan</td>
                        <td class="text-right text-slate-500"></td>
                        <td class="text-right font-medium text-rose-600 px-4">-Rp {{ number_format($totalDiscount, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="font-bold bg-blue-50/50">
                        <td class="px-4 py-3 text-blue-900">Total Penjualan Bersih (Net Revenue)</td>
                        <td></td>
                        <td class="text-right font-black text-blue-900 px-4">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                    </tr>

                    <!-- Bagian 2: HARGA POKOK PENJUALAN (HPP) -->
                    <tr class="bg-slate-50 font-bold text-slate-800 text-xs uppercase tracking-wider">
                        <td colspan="2" class="py-3 px-4">II. HARGA POKOK PENJUALAN (HPP)</td>
                        <td class="text-right py-3 px-4"></td>
                    </tr>
                    <tr>
                        <td class="px-5 py-3 text-slate-700">Beban Pokok Barang (Biaya Modal Produk yang Laku)</td>
                        <td class="text-right text-slate-500"></td>
                        <td class="text-right font-medium text-rose-600 px-4">-Rp {{ number_format($totalHpp, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="font-bold bg-indigo-50/50">
                        <td class="px-4 py-3 text-indigo-900">LABA KOTOR (GROSS PROFIT)</td>
                        <td></td>
                        <td class="text-right font-black text-indigo-900 px-4">Rp {{ number_format($totalGrossProfit, 0, ',', '.') }}</td>
                    </tr>

                    <!-- Bagian 3: BEBAN OPERASIONAL -->
                    <tr class="bg-slate-50 font-bold text-slate-800 text-xs uppercase tracking-wider">
                        <td colspan="2" class="py-3 px-4 d-flex justify-content-between align-items-center">
                            <span>III. BEBAN OPERASIONAL TOKO</span>
                            <a href="{{ route('expenses.index') }}" class="btn btn-outline-primary btn-sm py-0 px-2 text-2xs font-bold rounded">
                                + Kelola Beban
                            </a>
                        </td>
                        <td class="text-right py-3 px-4"></td>
                    </tr>
                    @forelse($expensesByCategory as $catName => $amount)
                    <tr>
                        <td class="px-5 py-2.5 text-slate-700">{{ $catName }}</td>
                        <td class="text-right text-slate-400 text-xs"></td>
                        <td class="text-right font-medium text-slate-700 px-4">Rp {{ number_format($amount, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-5 py-2 text-slate-400 text-xs">Belum ada catatan beban operasional pada periode ini.</td>
                    </tr>
                    @endforelse
                    <tr class="font-bold bg-amber-50/50">
                        <td class="px-4 py-3 text-amber-900">Total Beban Operasional</td>
                        <td></td>
                        <td class="text-right font-black text-rose-600 px-4">-Rp {{ number_format($totalExpenses, 0, ',', '.') }}</td>
                    </tr>

                    <!-- Bagian 4: TOTAL LABA / RUGI BERSIH AKHIR -->
                    <tr class="font-black {{ $isLoss ? 'bg-rose-100 text-rose-900' : 'bg-emerald-100 text-emerald-900' }}" style="font-size: 1.1rem;">
                        <td class="px-4 py-4 uppercase">
                            <i class="fa-solid {{ $isLoss ? 'fa-triangle-exclamation text-rose-600' : 'fa-circle-check text-emerald-600' }} mr-2"></i>
                            TOTAL LABA BERSIH (NET PROFIT / LOSS)
                        </td>
                        <td class="text-right text-xs font-semibold py-4">
                            Margin Bersih: {{ $totalRevenue > 0 ? number_format(($totalNetProfit / $totalRevenue) * 100, 1) : 0 }}%
                        </td>
                        <td class="text-right px-4 py-4 text-2xl">
                            Rp {{ number_format($totalNetProfit, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detailed Expense Breakdown Table -->
    <div class="card border-0 shadow-sm rounded-2xl bg-white mb-4">
        <div class="card-header bg-white border-bottom border-slate-100 p-4 d-flex justify-content-between align-items-center">
            <div>
                <h6 class="font-bold text-slate-800 mb-0">Rincian Beban Operasional Periode Ini</h6>
                <span class="text-xs text-slate-400">Total {{ $expenses->count() }} transaksi pengeluaran operasional</span>
            </div>
            <a href="{{ route('expenses.index') }}" class="btn btn-outline-primary btn-sm rounded-lg font-bold text-xs">
                <i class="fa-solid fa-plus mr-1"></i> Tambah Beban
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold">
                    <tr>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3">Kategori</th>
                        <th class="py-3">Keterangan / Keperluan</th>
                        <th class="py-3">Dicatat Oleh</th>
                        <th class="py-3 text-right">Nominal (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($expenses as $exp)
                    <tr>
                        <td class="py-3 px-4 text-xs text-slate-600">{{ $exp->expense_date->format('d/m/Y') }}</td>
                        <td class="py-3"><span class="badge badge-light border text-slate-700 font-semibold px-2 py-1">{{ $exp->category }}</span></td>
                        <td class="py-3 font-semibold text-slate-800">{{ $exp->title }}</td>
                        <td class="py-3 text-xs text-slate-500">{{ $exp->user->name ?? 'Admin' }}</td>
                        <td class="py-3 text-right font-bold text-rose-600">Rp {{ number_format($exp->amount, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-slate-400 text-xs">Tidak ada data beban operasional pada periode terpilih.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
