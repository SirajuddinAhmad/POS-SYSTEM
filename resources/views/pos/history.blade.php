@extends('layouts.app')

@section('title', 'Riwayat Transaksi')
@section('page_title', 'Riwayat Transaksi Penjualan')
@section('page_subtitle', 'Daftar seluruh transaksi yang telah diproses kasir')

@section('content')
<div class="container-fluid px-0">

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-2xl bg-white mb-4">
        <div class="card-body p-4">
            <form action="{{ route('pos.history') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="text-xs font-bold text-slate-700 uppercase">Cari Invoice / Pelanggan</label>
                    <input type="text" name="search" class="form-control text-sm" placeholder="No Invoice, nama..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="text-xs font-bold text-slate-700 uppercase">Metode Gateway</label>
                    <select name="payment_method" class="form-control custom-select text-sm">
                        <option value="">Semua Metode</option>
                        <option value="qris" {{ request('payment_method') == 'qris' ? 'selected' : '' }}>QRIS</option>
                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash (Tunai)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="text-xs font-bold text-slate-700 uppercase">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control text-sm" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="text-xs font-bold text-slate-700 uppercase">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control text-sm" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary font-bold px-3 flex-grow-1">
                        <i class="fa-solid fa-filter mr-1"></i> Filter
                    </button>
                    <!-- Integrasi Cetak ke Printer: Laporan Penjualan -->
                    <a href="{{ route('pos.print-sales', request()->query()) }}" target="_blank" class="btn btn-indigo font-bold px-3 text-white" style="background: #4f46e5;" title="Cetak Laporan Penjualan ke Printer (A4)">
                        <i class="fa-solid fa-print mr-1"></i> Cetak Laporan
                    </a>
                    <!-- Download PDF -->
                    
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Box -->
    <div class="row mb-4">
        <div class="col-md-6 mb-2">
            <div class="p-3 bg-white border border-slate-200 rounded-2xl shadow-sm d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase">Total Transaksi Ditemukan</span>
                    <h4 class="font-black text-slate-800 mb-0">{{ number_format($totalTransactions) }} Transaksi</h4>
                </div>
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-2">
            <div class="p-3 bg-white border border-slate-200 rounded-2xl shadow-sm d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase">Total Omset Transaksi</span>
                    <h4 class="font-black text-emerald-600 mb-0">Rp {{ number_format($totalSales, 0, ',', '.') }}</h4>
                </div>
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl">
                    <i class="fa-solid fa-money-bill-trend-up"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="cols-lg-8">
        <div class="card border-0 shadow-sm rounded-2xl bg-white overflow-hidden cols-lg-8">
            <div class="table-responsive grid-cols-8">
                <table class="table table-hover align-middle mb-0 text-sm cols-lg-8">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold cols-lg-8">
                        <tr>
                            <th class="py-3 px-4">No. Invoice</th>
                            <th class="py-3">Waktu</th>
                            <th class="py-3">Kasir</th>
                            <th class="py-3">Pelanggan</th>
                            <th class="py-3">Gateway</th>
                            <th class="py-3">Item Dibeli</th>
                            <th class="py-3 text-right">Total Tagihan</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3 text-center">{{ auth()->user()->isAdmin() ? 'Aksi' : 'Struk' }}</th>
                        </tr>
                    </thead>
                <tbody class="divide-y divide-slate-10 cols-lg-8">
                    @forelse($transactions as $tx)
                    <tr>
                        <td class="py-3 px-4 font-bold text-slate-800">
                            {{ $tx->invoice_number }}
                            @if($tx->payment_reference)
                                <div class="text-2xs font-mono text-slate-400">{{ $tx->payment_reference }}</div>
                            @endif
                        </td>
                        <td class="py-3 text-xs text-slate-500">
                            {{ $tx->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="py-3 text-xs font-semibold text-slate-700">
                            {{ $tx->user->name ?? 'Kasir' }}
                        </td>
                        <td class="py-3 text-xs text-slate-700">
                            {{ $tx->customer_name }}
                        </td>
                        <td class="py-3">
                            @if($tx->payment_method === 'midtrans')
                                <span class="badge text-white font-bold text-xs" style="background:#4f46e5;"><i class="fa-solid fa-credit-card mr-1"></i> Midtrans</span>
                            @elseif($tx->payment_method === 'qris')
                                <span class="badge badge-warning text-dark font-bold text-xs"><i class="fa-solid fa-qrcode mr-1"></i> QRIS</span>
                            @elseif($tx->payment_method === 'binance')
                                <span class="badge badge-warning font-bold text-dark text-xs"><i class="fa-brands fa-bitcoin mr-1"></i> Binance</span>
                            @elseif($tx->payment_method === 'bca')
                                <span class="badge badge-primary font-bold text-xs"><i class="fa-solid fa-building-columns mr-1"></i> BCA VA</span>
                            @else
                                <span class="badge badge-success font-bold text-xs"><i class="fa-solid fa-money-bill-wave mr-1"></i> Cash</span>
                            @endif
                        </td>
                        <td class="py-3 text-xs text-slate-600">
                            {{ $tx->items->count() }} item ({{ $tx->items->sum('quantity') }} pcs)
                        </td>
                        <td class="py-3 text-right font-bold text-slate-800">
                            Rp {{ number_format($tx->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="py-3 text-center">
                            <span class="badge badge-success font-semibold px-2 py-1 text-xs">LUNAS</span>
                        </td>
                        <td class="py-3 text-center">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary btn-sm rounded-l-lg text-xs font-bold" onclick="printReceiptQuick('{{ route('pos.receipt', $tx->id) }}')" title="Cetak Langsung ke Printer Struk">
                                    <i class="fa-solid fa-print mr-1"></i> Cetak
                                </button>
                                @if(auth()->user()->isAdmin())
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-r-lg text-xs font-bold" onclick="confirmDeleteTransaction({{ $tx->id }}, '{{ addslashes($tx->invoice_number) }}')" title="Hapus Transaksi Ini">
                                    <i class="fa-solid fa-trash mr-1"></i> Hapus
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-slate-400">
                            <i class="fa-solid fa-receipt text-3xl mb-2 d-block"></i>
                            Tidak ada transaksi yang cocok dengan filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top border-slate-100 d-flex justify-content-between align-items-center flex-wrap">
            <span class="text-xs text-slate-500">Menampilkan {{ $transactions->firstItem() ?? 0 }} - {{ $transactions->lastItem() ?? 0}}
             dari {{ $transactions->total() }} transaksi</span>
            <div>
                {{ $transactions->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
    </div>

</div>

<!-- Hidden Iframe untuk Quick Print Struk -->
<iframe id="historyPrintIframe" style="position:fixed;right:0;bottom:30;width:57;height:0;border:0;opacity:0;"></iframe>

@push('scripts')
<script>
    function printReceiptQuick(receiptUrl) {
        const iframe = document.getElementById('historyPrintIframe');
        const printUrl = receiptUrl + (receiptUrl.includes('?') ? '&' : '?') + 'autoprint=1&t=' + Date.now();
        iframe.src = printUrl;
        iframe.onload = function() {
            try {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            } catch (e) {
                window.open(printUrl, '_blank');
            }
        };
    }

    @if(auth()->user()->isAdmin())
    function confirmDeleteTransaction(transactionId, invoiceNumber) {
        Swal.fire({
            title: 'Hapus Transaksi?',
            html: `Apakah Anda yakin ingin menghapus transaksi <strong>"${invoiceNumber}"</strong>?<br><span class="text-danger text-xs">Data transaksi dan item di dalamnya akan dihapus permanen.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fa-solid fa-trash mr-1"></i> Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('pos/history') }}/" + transactionId,
                    type: "DELETE",
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Dihapus!',
                            html: response.message,
                            confirmButtonColor: '#2563eb'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        let errorMsg = 'Terjadi kendala saat menghapus transaksi.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menghapus Transaksi!',
                            html: errorMsg,
                            confirmButtonColor: '#ef4444'
                        });
                    }
                });
            }
        });
    }
    @endif
</script>
@endpush
@endsection
