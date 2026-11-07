@extends('layouts.app')

@section('title', 'Dashboard Administrator')
@section('page_title', 'Dashboard Administrator')
@section('page_subtitle', 'Ringkasan performa penjualan, laba kotor, dan inventaris toko')

@section('content')
<div class="container-fluid px-0">

    <!-- Top Key Metrics Cards -->
    <div class="row mb-4">
        <!-- 1. Total Produk -->
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card h-120 bg-white border-0 shadow-sm rounded-2xl overflow-hidden hover:shadow-md transition-shadow">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl font-bold flex-shrink-0 mr-3">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <div class="overflow-hidden">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Produk</span>
                        <h3 class="text-2xl font-black text-slate-800 mb-0">{{ number_format($totalProducts) }}</h3>
                        <span class="text-xs text-slate-500 font-medium">
                            <i class="fa-solid fa-triangle-exclamation text-amber-500"></i> {{ $lowStockProductsCount }} stok menipis
                        </span>
                    </div>
                </div>
                <div class="bg-blue-50 px-4 py-2 text-xs font-semibold text-blue-700 d-flex justify-content-between align-items-center">
                    <span>Lihat Katalog Produk</span>
                    <a href="{{ route('products.index') }}" class="text-blue-700 hover:text-blue-900"><i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <!-- 2. Total Penjualan -->
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card h-100 bg-white border-0 shadow-sm rounded-2xl overflow-hidden hover:shadow-md transition-shadow">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl font-bold flex-shrink-0 mr-3">
                        <i class="fa-solid fa-sack-dollar"></i>
                    </div>
                    <div class="overflow-hidden">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Penjualan</span>
                        <h3 class="text-2xl font-black text-emerald-600 mb-0">Rp {{ number_format($totalSalesAmount, 0, ',', '.') }}</h3>
                        <span class="text-xs text-slate-500 font-medium">
                            <i class="fa-solid fa-receipt text-slate-400"></i> {{ number_format($totalTransactionsCount) }} Transaksi Berhasil
                        </span>
                    </div>
                </div>
                <div class="bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-700 d-flex justify-content-between align-items-center">
                    <span>Penjualan Hari Ini: <strong>Rp {{ number_format($todaySalesAmount, 0, ',', '.') }}</strong></span>
                    <a href="{{ route('pos.history') }}" class="text-emerald-700 hover:text-emerald-900"><i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <!-- 3. Total Laba Kotor -->
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card h-100 bg-white border-0 shadow-sm rounded-2xl overflow-hidden hover:shadow-md transition-shadow">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl font-bold flex-shrink-0 mr-3">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <div class="overflow-hidden">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Laba Kotor</span>
                        <h3 class="text-2xl font-black text-indigo-600 mb-0">Rp {{ number_format($totalGrossProfit, 0, ',', '.') }}</h3>
                        <span class="text-xs text-slate-500 font-medium">
                            @php
                                $marginPercent = $totalSalesAmount > 0 ? ($totalGrossProfit / $totalSalesAmount) * 100 : 0;
                            @endphp
                            Margin Kotor: <strong class="text-indigo-600">{{ number_format($marginPercent, 1) }}%</strong>
                        </span>
                    </div>
                </div>
                <div class="bg-indigo-50 px-4 py-2 text-xs font-semibold text-indigo-700 d-flex justify-content-between align-items-center">
                    <span>Laba Bersih & Rugi Lengkap</span>
                    <a href="{{ route('reports.profit-loss') }}" class="text-indigo-700 hover:text-indigo-900"><i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
        </div>


    </div>

    <!-- Charts Section: Traffic & Top Trending -->
    <div class="row mb-4">
        <!-- 4. Traffic Penjualan -->
        <div class="col-lg-7 mb-4">
            <div class="card border-0 shadow-sm rounded-2xl bg-white h-100">
                <div class="card-header bg-white border-bottom border-slate-100 py-3 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="font-bold text-slate-800 mb-0">
                            <i class="fa-solid fa-chart-column text-blue-600 mr-2"></i> 4. Traffic Penjualan (7 Hari Terakhir)
                        </h6>
                        <span class="text-xs text-slate-400">Grafik pergerakan omset dan laba kotor harian</span>
                    </div>
                    <div class="text-xs font-semibold text-slate-500">
                        <span class="badge badge-primary mr-1"><i class="fa-solid fa-circle text-xs mr-1"></i> Omset</span>
                        <span class="badge badge-info"><i class="fa-solid fa-circle text-xs mr-1"></i> Laba</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div style="height: 300px;">
                        <canvas id="salesTrafficChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Grafik Top Trending Produk -->
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm rounded-2xl bg-white h-100">
                <div class="card-header bg-white border-bottom border-slate-100 py-3 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="font-bold text-slate-800 mb-0">
                            <i class="fa-solid fa-fire-flame-curved text-amber-500 mr-2"></i> 5. Grafik Top Trending Produk
                        </h6>
                        <span class="text-xs text-slate-400">Produk paling laris terjual berdasarkan kuantitas</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div style="height: 300px;">
                        <canvas id="topProductsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Tables: Recent Transactions & Low Stock Alert -->
    <div class="row">
        <!-- Recent Transactions -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm rounded-2xl bg-white">
                <div class="card-header bg-white border-bottom border-slate-100 py-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="font-bold text-slate-800 mb-0">
                        <i class="fa-solid fa-clock-rotate-left text-slate-500 mr-2"></i> Transaksi Terbaru
                    </h6>
                    <a href="{{ route('pos.history') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">
                        Lihat Semua Riwayat <i class="fa-solid fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-sm">
                        <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold">
                            <tr>
                                <th class="py-3 px-4">Invoice</th>
                                <th class="py-3">Kasir / Pelanggan</th>
                                <th class="py-3">Metode</th>
                                <th class="py-3 text-right">Total</th>
                                <th class="py-3 text-center">Status</th>
                                <th class="py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentTransactions as $tx)
                            <tr>
                                <td class="py-3 px-4 font-bold text-slate-800">
                                    {{ $tx->invoice_number }}
                                    <div class="text-xs font-normal text-slate-400">{{ $tx->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="py-3">
                                    <div class="font-medium text-slate-700">{{ $tx->customer_name }}</div>
                                    <div class="text-xs text-slate-400"><i class="fa-solid fa-user-tag text-xs mr-1"></i> {{ $tx->user->name ?? 'Kasir' }}</div>
                                </td>
                                <td class="py-3">
                                    @if($tx->payment_method === 'qris')
                                        <span class="badge badge-warning text-dark font-semibold"><i class="fa-solid fa-qrcode mr-1"></i> QRIS</span>
                                    @elseif($tx->payment_method === 'binance')
                                        <span class="badge badge-warning font-semibold text-dark"><i class="fa-brands fa-bitcoin mr-1"></i> Binance Pay</span>
                                    @elseif($tx->payment_method === 'bca')
                                        <span class="badge badge-primary font-semibold"><i class="fa-solid fa-building-columns mr-1"></i> BCA VA</span>
                                    @else
                                        <span class="badge badge-success font-semibold"><i class="fa-solid fa-money-bill-wave mr-1"></i> Cash</span>
                                    @endif
                                </td>
                                <td class="py-3 text-right font-bold text-slate-800">
                                    Rp {{ number_format($tx->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3 text-center">
                                    <span class="badge badge-success font-semibold px-2 py-1">LUNAS</span>
                                </td>
                                <td class="py-3 text-center">
                                    <a href="{{ route('pos.receipt', $tx->id) }}" target="_blank" class="btn btn-light btn-sm rounded-lg text-xs" title="Cetak Struk">
                                        <i class="fa-solid fa-print"></i> Struk
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-slate-400">Belum ada transaksi</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Low Stock Warning Table -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm rounded-2xl bg-white">
                <div class="card-header bg-white border-bottom border-slate-100 py-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="font-bold text-slate-800 mb-0">
                        <i class="fa-solid fa-triangle-exclamation text-amber-500 mr-2"></i> Peringatan Stok Menipis
                    </h6>
                    <a href="{{ route('products.index', ['stock_status' => 'low']) }}" class="text-xs font-semibold text-amber-600 hover:text-amber-800">
                        Kelola
                    </a>
                </div>
                <div class="p-3">
                    @forelse($lowStockProducts as $prod)
                    <div class="d-flex align-items-center justify-content-between p-2 mb-2 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="d-flex align-items-center">
                            <img src="{{ $prod->image_url }}" class="w-10 h-10 rounded-lg object-cover mr-2 border border-slate-200" onerror="this.onerror=null; this.src='{{ asset('images/default-product.svg') }}';">
                            <div>
                                <h6 class="font-bold text-slate-800 text-xs mb-0">{{ $prod->name }}</h6>
                                <span class="text-xs text-slate-400">{{ $prod->code }} &bull; {{ $prod->category->name ?? 'Umum' }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="badge badge-danger font-bold text-xs px-2 py-1">
                                Sisa: {{ $prod->stock }} {{ $prod->unit }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-slate-400 text-xs">
                        <i class="fa-solid fa-circle-check text-success text-2xl mb-2 d-block"></i>
                        Semua stok produk dalam kondisi aman!
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 4. Traffic Penjualan Chart (Line / Area)
    const trafficCtx = document.getElementById('salesTrafficChart').getContext('2d');
    const trafficLabels = {!! json_encode($salesTrafficLabels) !!};
    const trafficSales = {!! json_encode($salesTrafficValues) !!};
    const trafficProfits = {!! json_encode($salesTrafficProfits) !!};

    new Chart(trafficCtx, {
        type: 'line',
        data: {
            labels: trafficLabels,
            datasets: [
                {
                    label: 'Total Penjualan (Rp)',
                    data: trafficSales,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 2.5,
                    tension: 0.35,
                    fill: true,
                    pointBackgroundColor: '#2563eb',
                    pointRadius: 4
                },
                {
                    label: 'Laba Kotor (Rp)',
                    data: trafficProfits,
                    borderColor: '#06b6d4',
                    backgroundColor: 'rgba(6, 182, 212, 0.08)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    tension: 0.35,
                    fill: true,
                    pointBackgroundColor: '#06b6d4',
                    pointRadius: 3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { font: { family: 'Plus Jakarta Sans', weight: '600' } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(val) {
                            return 'Rp ' + (val / 1000) + 'k';
                        }
                    },
                    grid: { color: '#f1f5f9' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // 5. Grafik Top Trending Produk (Horizontal Bar Chart)
    const topCtx = document.getElementById('topProductsChart').getContext('2d');
    const topLabels = {!! json_encode($topProductLabels) !!};
    const topQuantities = {!! json_encode($topProductQuantities) !!};

    new Chart(topCtx, {
        type: 'bar',
        data: {
            labels: topLabels,
            datasets: [{
                label: 'Qty Terjual',
                data: topQuantities,
                backgroundColor: [
                    '#3b82f6',
                    '#10b981',
                    '#f59e0b',
                    '#8b5cf6',
                    '#ec4899'
                ],
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Terjual: ' + context.raw + ' pcs/unit';
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 },
                    grid: { color: '#f1f5f9' }
                },
                y: {
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endpush
