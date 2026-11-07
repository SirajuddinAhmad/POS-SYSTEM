@extends('layouts.app')

@section('title', 'Katalog Produk')
@section('page_title', 'Manajemen Produk')
@section('page_subtitle', 'Kelola daftar produk, harga modal, harga jual, dan stok barang')

@section('content')
<div class="container-fluid px-0">

    <!-- Action & Filter Bar -->
    <div class="card border-0 shadow-sm rounded-2xl bg-white mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <!-- Search & Filters -->
                <div class="col-lg-8 mb-3 mb-lg-0">
                    <form action="{{ route('products.index') }}" method="GET" class="form-row align-items-center">
                        <div class="col-md-4 mb-2 mb-md-0">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white border-right-0 text-slate-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                                </div>
                                <input type="text" name="search" class="form-control border-left-0" placeholder="Cari nama, SKU, barcode..." value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-md-3 mb-2 mb-md-0">
                            <select name="category_id" class="form-control custom-select" onchange="this.form.submit()">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mb-2 mb-md-0">
                            <select name="stock_status" class="form-control custom-select" onchange="this.form.submit()">
                                <option value="">Semua Status Stok</option>
                                <option value="in" {{ request('stock_status') == 'in' ? 'selected' : '' }}>Stok Aman</option>
                                <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Stok Menipis (<= Min)</option>
                                <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Habis (0)</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-secondary btn-block font-semibold">
                                Filter
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Add Product Button (3. Tambah Produk) -->
                <div class="col-lg-4 text-lg-right">
                    <button type="button" class="btn btn-primary rounded-xl font-bold px-4 py-2 shadow-sm" data-toggle="modal" data-target="#addProductModal">
                        <i class="fa-solid fa-plus-circle mr-1"></i> Tambah Produk Baru
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 1. Read Produk Table -->
    <div class="card border-0 shadow-sm rounded-2xl bg-white overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold">
                    <tr>
                        <th class="py-3 px-4" style="width: 70px;">Foto</th>
                        <th class="py-3">Info Produk / SKU</th>
                        <th class="py-3">Kategori</th>
                        <th class="py-3 text-right">Harga Modal</th>
                        <th class="py-3 text-right">Harga Jual</th>
                        <th class="py-3 text-right">Margin / Laba</th>
                        <th class="py-3 text-center">Stok</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3 text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $product)
                    <tr>
                        <td class="py-3 px-4">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-20 h-20 rounded-xl object-cover border border-slate-200" onerror="this.onerror=null; this.src='{{ asset('images/default-product.svg') }}';">
                        </td>
                        <td class="py-3">
                            <div class="font-bold text-slate-800">{{ $product->name }}</div>
                            <div class="text-xs text-slate-400">
                                <span class="badge badge-light border border-slate-200 font-mono text-xs">{{ $product->code }}</span>
                                @if($product->barcode)
                                    &bull; <i class="fa-solid fa-barcode text-slate-400"></i> {{ $product->barcode }}
                                @endif
                            </div>
                        </td>
                        <td class="py-3">
                            <span class="badge badge-light border text-slate-700 font-semibold px-2 py-1">
                                {{ $product->category->name ?? 'Tanpa Kategori' }}
                            </span>
                        </td>
                        <td class="py-3 text-right font-medium text-slate-500">
                            Rp {{ number_format($product->cost_price, 0, ',', '.') }}
                        </td>
                        <td class="py-3 text-right font-bold text-slate-800">
                            Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                        </td>
                        <td class="py-3 text-right font-semibold text-emerald-600">
                            +Rp {{ number_format($product->margin, 0, ',', '.') }}
                        </td>
                        <td class="py-3 text-center">
                            @if($product->stock <= 0)
                                <span class="badge badge-danger px-2.5 py-1 font-bold">Habis (0)</span>
                            @elseif($product->stock <= $product->min_stock)
                                <span class="badge badge-warning text-dark px-2.5 py-1 font-bold">
                                    {{ $product->stock }} {{ $product->unit }} (Kritis)
                                </span>
                            @else
                                <span class="badge badge-success px-2.5 py-1 font-bold">
                                    {{ $product->stock }} {{ $product->unit }}
                                </span>
                            @endif
                        </td>
                        <td class="py-3 text-center">
                            @if($product->is_active)
                                <span class="badge badge-pill badge-success text-xs">Aktif</span>
                            @else
                                <span class="badge badge-pill badge-secondary text-xs">Nonaktif</span>
                            @endif
                        </td>
                        <td class="py-3 text-center">
                            <div class="btn-group btn-group-sm">
                                <!-- 2. Edit Produk Button -->
                                <button type="button" class="btn btn-outline-primary rounded-lg mr-1" onclick="openEditModal({{ $product->id }})" title="Edit Produk">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <!-- 4. Hapus Produk Button -->
                                <button type="button" class="btn btn-outline-danger rounded-lg" onclick="confirmDeleteProduct({{ $product->id }}, '{{ addslashes($product->name) }}')" title="Hapus Produk">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-slate-400">
                            <i class="fa-solid fa-box-open text-4xl mb-2 d-block"></i>
                            Tidak ada produk yang sesuai dengan kriteria pencarian.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top border-slate-100 d-flex justify-content-between align-items-center flex-wrap">
            <span class="text-xs text-slate-500">Menampilkan {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} dari total {{ $products->total() }} produk</span>
            <div>
                {{ $products->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

</div>

<!-- 3. Modal Tambah Produk + SweetAlert pop up -->
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 rounded-2xl shadow-xl overflow-hidden">
            <div class="modal-header bg-blue-600 text-white py-3 px-4">
                <h5 class="modal-title font-bold text-white"><i class="fa-solid fa-circle-plus mr-2"></i> Tambah Produk Baru</h5>
                <button type="button" class="close text-white opacity-90" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formAddProduct" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="text-xs font-bold text-slate-700 uppercase">Kode SKU / Produk <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" placeholder="Contoh: PRD-012" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="text-xs font-bold text-slate-700 uppercase">Barcode EAN / Scanner (Opsional)</label>
                            <input type="text" name="barcode" class="form-control" placeholder="Contoh: 899276111112">
                        </div>
                        <div class="col-md-8 form-group">
                            <label class="text-xs font-bold text-slate-700 uppercase">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: Kopi Robusta 100g" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="text-xs font-bold text-slate-700 uppercase">Kategori</label>
                            <select name="category_id" class="form-control custom-select">
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 form-group">
                            <label class="text-xs font-bold text-slate-700 uppercase">Harga Modal (Rp) <span class="text-danger">*</span></label>
                            <input type="number" step="100" name="cost_price" class="form-control" placeholder="0" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="text-xs font-bold text-slate-700 uppercase">Harga Jual (Rp) <span class="text-danger">*</span></label>
                            <input type="number" step="100" name="selling_price" class="form-control" placeholder="0" required>
                        </div>
                        <div class="col-md-2 form-group">
                            <label class="text-xs font-bold text-slate-700 uppercase">Stok Awal <span class="text-danger">*</span></label>
                            <input type="number" name="stock" class="form-control" value="10" min="0" required>
                        </div>
                        <div class="col-md-2 form-group">
                            <label class="text-xs font-bold text-slate-700 uppercase">Satuan</label>
                            <input type="text" name="unit" class="form-control" value="pcs" required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="text-xs font-bold text-slate-700 uppercase">Batas Stok Minimum</label>
                            <input type="number" name="min_stock" class="form-control" value="5" min="1">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="text-xs font-bold text-slate-700 uppercase">Foto Produk (Opsional)</label>
                            <input type="file" name="image" class="form-control-file border p-1 rounded">
                        </div>

                        <div class="col-12 form-group">
                            <label class="text-xs font-bold text-slate-700 uppercase">Deskripsi / Catatan Produk</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Keterangan singkat produk..."></textarea>
                        </div>

                        <div class="col-12 form-group mb-0">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_active_add" name="is_active" value="1" checked>
                                <label class="custom-control-label font-medium text-slate-700" for="is_active_add">Produk Aktif & Siap Dijual di Kasir</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-slate-50 py-3 px-4">
                    <button type="button" class="btn btn-secondary font-semibold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-bold px-4" id="btnSaveAddProduct">
                        <i class="fa-solid fa-save mr-1"></i> Simpan Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. Modal Edit Produk + SweetAlert pop up -->
<div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 rounded-2xl shadow-xl overflow-hidden">
            <div class="modal-header bg-indigo-600 text-white py-3 px-4">
                <h5 class="modal-title font-bold text-white"><i class="fa-solid fa-pen-to-square mr-2"></i> Edit Data Produk</h5>
                <button type="button" class="close text-white opacity-90" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formEditProduct" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_product_id">
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="text-xs font-bold text-slate-700 uppercase">Kode SKU / Produk <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="edit_code" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="text-xs font-bold text-slate-700 uppercase">Barcode EAN / Scanner</label>
                            <input type="text" name="barcode" id="edit_barcode" class="form-control">
                        </div>
                        <div class="col-md-8 form-group">
                            <label class="text-xs font-bold text-slate-700 uppercase">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="text-xs font-bold text-slate-700 uppercase">Kategori</label>
                            <select name="category_id" id="edit_category_id" class="form-control custom-select">
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 form-group">
                            <label class="text-xs font-bold text-slate-700 uppercase">Harga Modal (Rp) <span class="text-danger">*</span></label>
                            <input type="number" step="100" name="cost_price" id="edit_cost_price" class="form-control" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="text-xs font-bold text-slate-700 uppercase">Harga Jual (Rp) <span class="text-danger">*</span></label>
                            <input type="number" step="100" name="selling_price" id="edit_selling_price" class="form-control" required>
                        </div>
                        <div class="col-md-2 form-group">
                            <label class="text-xs font-bold text-slate-700 uppercase">Stok <span class="text-danger">*</span></label>
                            <input type="number" name="stock" id="edit_stock" class="form-control" min="0" required>
                        </div>
                        <div class="col-md-2 form-group">
                            <label class="text-xs font-bold text-slate-700 uppercase">Satuan</label>
                            <input type="text" name="unit" id="edit_unit" class="form-control" required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="text-xs font-bold text-slate-700 uppercase">Batas Stok Minimum</label>
                            <input type="number" name="min_stock" id="edit_min_stock" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="text-xs font-bold text-slate-700 uppercase">Ganti Foto Produk</label>
                            <input type="file" name="image" class="form-control-file border p-1 rounded">
                        </div>

                        <div class="col-12 form-group">
                            <label class="text-xs font-bold text-slate-700 uppercase">Deskripsi</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="col-12 form-group mb-0">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="edit_is_active" name="is_active" value="1">
                                <label class="custom-control-label font-medium text-slate-700" for="edit_is_active">Produk Aktif & Siap Dijual</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-slate-50 py-3 px-4">
                    <button type="button" class="btn btn-secondary font-semibold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-indigo font-bold px-4 bg-indigo-600 text-white" id="btnSaveEditProduct">
                        <i class="fa-solid fa-check mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 4. Hidden Form for Delete -->
<form id="deleteProductForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
    // AJAX Submission for Add Product (Fitur 3: pop up berhasil atau tidak di tambah)
    $('#formAddProduct').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const submitBtn = $('#btnSaveAddProduct');
        submitBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i> Menyimpan...');

        $.ajax({
            url: "{{ route('products.store') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#addProductModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Ditambah!',
                    html: response.message,
                    confirmButtonColor: '#2563eb'
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html('<i class="fa-solid fa-save mr-1"></i> Simpan Produk');
                let errorMsg = 'Terjadi kesalahan sistem.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menambah Produk!',
                    html: errorMsg,
                    confirmButtonColor: '#ef4444'
                });
            }
        });
    });

    // Fitur 2: Load & Edit Product via Modal + pop up berhasil atau tidak di edit
    function openEditModal(productId) {
        $.ajax({
            url: "{{ url('products') }}/" + productId,
            type: "GET",
            dataType: "json",
            success: function(data) {
                $('#edit_product_id').val(data.id);
                $('#edit_code').val(data.code);
                $('#edit_barcode').val(data.barcode);
                $('#edit_name').val(data.name);
                $('#edit_category_id').val(data.category_id);
                $('#edit_cost_price').val(data.cost_price);
                $('#edit_selling_price').val(data.selling_price);
                $('#edit_stock').val(data.stock);
                $('#edit_unit').val(data.unit);
                $('#edit_min_stock').val(data.min_stock);
                $('#edit_description').val(data.description);
                $('#edit_is_active').prop('checked', data.is_active == 1);

                $('#formEditProduct').attr('action', "{{ url('products') }}/" + data.id);
                $('#editProductModal').modal('show');
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memuat Data Produk!',
                    text: 'Tidak dapat mengambil detail produk dari server.',
                    confirmButtonColor: '#ef4444'
                });
            }
        });
    }

    // AJAX Submission for Edit Product
    $('#formEditProduct').on('submit', function(e) {
        e.preventDefault();
        const actionUrl = $(this).attr('action');
        const formData = new FormData(this);
        const submitBtn = $('#btnSaveEditProduct');
        submitBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i> Memperbarui...');

        $.ajax({
            url: actionUrl,
            type: "POST", // Form contains _method=PUT
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#editProductModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Diedit!',
                    html: response.message,
                    confirmButtonColor: '#4f46e5'
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html('<i class="fa-solid fa-check mr-1"></i> Simpan Perubahan');
                let errorMsg = 'Terjadi kesalahan saat mengedit data.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengedit Produk!',
                    html: errorMsg,
                    confirmButtonColor: '#ef4444'
                });
            }
        });
    });

    // Fitur 4: Hapus Produk + pop up berhasil atau tidak di hapus
    function confirmDeleteProduct(productId, productName) {
        Swal.fire({
            title: 'Hapus Produk?',
            html: `Apakah Anda yakin ingin menghapus produk <strong>"${productName}"</strong>?<br><span class="text-danger text-xs">Tindakan ini tidak dapat dibatalkan.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fa-solid fa-trash mr-1"></i> Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('products') }}/" + productId,
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
                        let errorMsg = 'Terjadi kendala saat menghapus produk.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menghapus Produk!',
                            html: errorMsg,
                            confirmButtonColor: '#ef4444'
                        });
                    }
                });
            }
        });
    }
</script>
@endpush
