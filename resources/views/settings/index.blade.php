@extends('layouts.app')

@section('title', 'Pengaturan Toko')
@section('page_title', 'Pengaturan Toko & Pembayaran')
@section('page_subtitle', 'Konfigurasi identitas toko pada struk, printer kasir, dan upload gambar QRIS')

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Store Identity Settings -->
                <div class="card border-0 shadow-sm rounded-2xl bg-white p-4 mb-4">
                    <h5 class="font-bold text-slate-800 mb-1">
                        <i class="fa-solid fa-store text-blue-600 mr-2"></i> Identitas Toko & Informasi Struk Kasir
                    </h5>
                    <p class="text-xs text-slate-400 mb-4">Informasi ini akan tercetak secara otomatis pada struk thermal dan dokumen PDF laporan.</p>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="text-xs font-bold text-slate-700 uppercase">Nama Toko / Bisnis <span class="text-danger">*</span></label>
                            <input type="text" name="store_name" class="form-control" value="{{ $settings['store_name'] }}" required>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="text-xs font-bold text-slate-700 uppercase">No. Telepon Toko <span class="text-danger">*</span></label>
                            <input type="text" name="store_phone" class="form-control" value="{{ $settings['store_phone'] }}" required>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="text-xs font-bold text-slate-700 uppercase">Email Toko</label>
                            <input type="email" name="store_email" class="form-control" value="{{ $settings['store_email'] }}">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="text-xs font-bold text-slate-700 uppercase">Alamat Lengkap Toko</label>
                            <input type="text" name="store_address" class="form-control" value="{{ $settings['store_address'] }}" required>
                        </div>
                        <div class="col-12 form-group mb-0">
                            <label class="text-xs font-bold text-slate-700 uppercase">Pesan Footer Struk (Thermal Receipt)</label>
                            <textarea name="store_footer" class="form-control" rows="2">{{ $settings['store_footer'] }}</textarea>
                            <small class="text-slate-400">Contoh: "Terima kasih atas kunjungan Anda! Simpan struk ini sebagai bukti pembayaran."</small>
                        </div>
                    </div>
                </div>

                <!-- Printer POS & Cetak Configuration -->
                <div class="card border-0 shadow-sm rounded-2xl bg-white p-4 mb-4">
                    <h5 class="font-bold text-slate-800 mb-1">
                        <i class="fa-solid fa-print text-indigo-600 mr-2"></i> Integrasi Printer Kasir & Cetak Dokumen
                    </h5>
                    <p class="text-xs text-slate-400 mb-4">Konfigurasi ukuran kertas thermal struk kasir dan perilaku dialog pencetakan otomatis.</p>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="text-xs font-bold text-slate-700 uppercase">Ukuran Kertas Default Struk Kasir <span class="text-danger">*</span></label>
                            <select name="receipt_paper_size" class="form-control custom-select">
                                <option value="80mm" {{ ($settings['receipt_paper_size'] ?? '80mm') === '80mm' ? 'selected' : '' }}>
                                    80mm (Thermal POS Standar - Epson, Panda, Xprinter, Star)
                                </option>
                                <option value="58mm" {{ ($settings['receipt_paper_size'] ?? '80mm') === '58mm' ? 'selected' : '' }}>
                                    58mm (Thermal POS Mini / Bluetooth Portable)
                                </option>
                            </select>
                            <small class="text-slate-400">Ukuran lebar kertas roll thermal yang digunakan printer kasir Anda.</small>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label class="text-xs font-bold text-slate-700 uppercase">Otomatisasi Cetak Struk Kasir</label>
                            <select name="receipt_auto_print" class="form-control custom-select">
                                <option value="1" {{ ($settings['receipt_auto_print'] ?? '1') == '1' ? 'selected' : '' }}>
                                    Ya - Buka dialog printer otomatis setelah pembayaran lunas
                                </option>
                                <option value="0" {{ ($settings['receipt_auto_print'] ?? '1') == '0' ? 'selected' : '' }}>
                                    Tidak - Cetak manual hanya saat tombol 'Cetak Struk' diklik
                                </option>
                            </select>
                            <small class="text-slate-400">Mempercepat proses transaksi kasir tanpa perlu klik tombol cetak berulang kali.</small>
                        </div>
                    </div>
                </div>

                <!-- Upload Gambar QRIS Dinamis -->
                <div class="card border-0 shadow-sm rounded-2xl bg-white p-4 mb-4">
                    <h5 class="font-bold text-slate-800 mb-1">
                        <i class="fa-solid fa-qrcode text-red-600 mr-2"></i> Upload Gambar QRIS Merchant
                    </h5>
                    <p class="text-xs text-slate-400 mb-4">Upload gambar QRIS dari bank atau penyedia pembayaran Anda (GoPay, Dana, OVO, BCA, dll). Gambar ini akan ditampilkan di layar kasir saat pelanggan memilih metode QRIS.</p>

                    <div class="row align-items-center">
                        <!-- Preview gambar QRIS saat ini -->
                        <div class="col-md-4 text-center mb-3 mb-md-0">
                            @if(!empty($settings['qris_image_path']) && file_exists(public_path($settings['qris_image_path'])))
                                <div class="p-2 border-2 border-dashed border-red-300 rounded-2xl d-inline-block bg-white shadow-sm">
                                    <img src="{{ asset($settings['qris_image_path']) }}"
                                         alt="QRIS Merchant"
                                         style="max-width: 180px; max-height: 180px; object-fit: contain;"
                                         class="rounded-xl">
                                </div>
                                <p class="text-xs text-emerald-600 font-semibold mt-2 mb-0">
                                    <i class="fa-solid fa-circle-check mr-1"></i> Gambar QRIS sudah diupload
                                </p>
                            @else
                                <div class="p-4 border-2 border-dashed border-slate-300 rounded-2xl bg-slate-50 text-slate-400 d-inline-block">
                                    <i class="fa-solid fa-qrcode text-5xl mb-2 d-block text-slate-300"></i>
                                    <span class="text-xs">Belum ada gambar QRIS</span>
                                </div>
                            @endif
                        </div>

                        <!-- Form upload -->
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label class="text-xs font-bold text-slate-700 uppercase">Upload Gambar QRIS Baru (JPG, PNG, WEBP — Maks. 2MB)</label>
                                <div class="custom-file">
                                    <input type="file"
                                           name="qris_image"
                                           id="qrisImageInput"
                                           class="custom-file-input"
                                           accept="image/jpeg,image/png,image/webp">
                                    <label class="custom-file-label text-sm text-slate-500" for="qrisImageInput">Pilih file gambar QRIS...</label>
                                </div>
                                <small class="text-slate-500 mt-1 d-block">
                                    <i class="fa-solid fa-circle-info mr-1 text-blue-500"></i>
                                    Gunakan gambar QRIS statis dari merchant Anda. Pelanggan akan scan gambar ini dari layar kasir.
                                </small>
                            </div>

                            <!-- Preview sebelum upload -->
                            <div id="qrisPreviewBox" class="d-none mb-3">
                                <label class="text-xs font-bold text-slate-700 uppercase">Preview Sebelum Simpan:</label>
                                <div class="p-2 border border-slate-200 rounded-xl bg-white d-inline-block shadow-sm">
                                    <img id="qrisPreviewImg" src="#" alt="Preview" style="max-width: 150px; max-height: 150px; object-fit: contain;">
                                </div>
                            </div>

                            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
                                <i class="fa-solid fa-lightbulb mr-1"></i>
                                <strong>Tips:</strong> Pastikan gambar QRIS terbaca jelas dan tidak blur. Ukuran ideal minimal 300×300 piksel dengan latar belakang putih.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary btn-lg rounded-xl font-bold px-5 py-3 shadow-md">
                        <i class="fa-solid fa-save mr-2"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Preview gambar QRIS sebelum upload
    document.getElementById('qrisImageInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        // Update custom file label
        document.querySelector('.custom-file-label').textContent = file.name;

        // Show preview
        const reader = new FileReader();
        reader.onload = function(evt) {
            document.getElementById('qrisPreviewImg').src = evt.target.result;
            document.getElementById('qrisPreviewBox').classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    });
</script>
@endpush
