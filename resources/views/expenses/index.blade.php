@extends('layouts.app')

@section('title', 'Beban Operasional')
@section('page_title', 'Beban Operasional Toko')
@section('page_subtitle', 'Catat pengeluaran listrik, sewa, gaji, dan kebutuhan operasional untuk perhitungan Laba Bersih')

@section('content')
<div class="container-fluid px-0">

    <!-- Top Card -->
    <div class="card border-0 shadow-sm rounded-2xl bg-white mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <form action="{{ route('expenses.index') }}" method="GET" class="form-row align-items-center">
                    <div class="col-auto mb-2 mb-md-0">
                        <select name="category" class="form-control custom-select text-sm" onchange="this.form.submit()">
                            <option value="">Semua Kategori Beban</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto mb-2 mb-md-0">
                        <input type="date" name="start_date" class="form-control form-control-sm text-sm" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-auto mb-2 mb-md-0">
                        <input type="date" name="end_date" class="form-control form-control-sm text-sm" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-secondary btn-sm font-semibold">Filter</button>
                    </div>
                </form>

                <div>
                    <button type="button" class="btn btn-primary rounded-xl font-bold px-4 py-2 shadow-sm" data-toggle="modal" data-target="#addExpenseModal">
                        <i class="fa-solid fa-plus-circle mr-1"></i> Catat Pengeluaran Baru
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Box -->
    <div class="card border-0 shadow-sm rounded-2xl bg-slate-900 text-white p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Beban Operasional Terfilter</span>
                <h3 class="text-3xl font-black text-white mb-0 mt-1">Rp {{ number_format($totalExpenseAmount, 0, ',', '.') }}</h3>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-blue-600/20 text-blue-400 flex items-center justify-center text-2xl">
                <i class="fa-solid fa-money-bill-transfer"></i>
            </div>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="card border-0 shadow-sm rounded-2xl bg-white overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold">
                    <tr>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3">Kategori</th>
                        <th class="py-3">Keperluan / Keterangan</th>
                        <th class="py-3">Dicatat Oleh</th>
                        <th class="py-3 text-right">Jumlah (Rp)</th>
                        <th class="py-3 text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($expenses as $exp)
                    <tr>
                        <td class="py-3 px-4 text-xs text-slate-600">{{ $exp->expense_date->format('d/m/Y') }}</td>
                        <td class="py-3">
                            <span class="badge badge-light border text-slate-700 font-semibold px-2 py-1 text-xs">{{ $exp->category }}</span>
                        </td>
                        <td class="py-3 font-semibold text-slate-800">
                            {{ $exp->title }}
                            @if($exp->notes)
                                <div class="text-2xs text-slate-400">{{ $exp->notes }}</div>
                            @endif
                        </td>
                        <td class="py-3 text-xs text-slate-500">{{ $exp->user->name ?? 'Admin' }}</td>
                        <td class="py-3 text-right font-bold text-rose-600">Rp {{ number_format($exp->amount, 0, ',', '.') }}</td>
                        <td class="py-3 text-center">
                            <form action="{{ route('expenses.destroy', $exp->id) }}" method="POST" onsubmit="return confirm('Hapus catatan pengeluaran ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-lg text-xs" title="Hapus">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-slate-400">Belum ada catatan pengeluaran operasional.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top border-slate-100">
            {{ $expenses->links('pagination::bootstrap-4') }}
        </div>
    </div>

</div>

<!-- Modal Add Expense -->
<div class="modal fade" id="addExpenseModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 rounded-2xl shadow-xl overflow-hidden">
            <div class="modal-header bg-blue-600 text-white py-3 px-4">
                <h5 class="modal-title font-bold text-white"><i class="fa-solid fa-money-bill-transfer mr-2"></i> Tambah Pengeluaran Operasional</h5>
                <button type="button" class="close text-white opacity-90" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('expenses.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="text-xs font-bold text-slate-700 uppercase">Tanggal Pengeluaran <span class="text-danger">*</span></label>
                        <input type="date" name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-xs font-bold text-slate-700 uppercase">Kategori Beban <span class="text-danger">*</span></label>
                        <input type="text" name="category" class="form-control" placeholder="Contoh: Listrik, Gaji Karyawan, Sewa Ruko, Wi-Fi" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-xs font-bold text-slate-700 uppercase">Judul / Keperluan <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Tagihan Listrik Toko Bulan Ini" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-xs font-bold text-slate-700 uppercase">Nominal Biaya (Rp) <span class="text-danger">*</span></label>
                        <input type="number" step="100" name="amount" class="form-control font-bold" placeholder="0" required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs font-bold text-slate-700 uppercase">Catatan Tambahan (Opsional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Catatan atau nomor nota..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-slate-50 py-3 px-4">
                    <button type="button" class="btn btn-secondary font-semibold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-bold px-4">Simpan Beban</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
