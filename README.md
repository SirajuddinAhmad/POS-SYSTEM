# POS SYSTEM MULTI-GATEWAY ONLINE (LARAVEL 11 + MYSQL)

Sistem Kasir & Point of Sale Modern multi-gateway yang siap digunakan untuk operasional toko retail, kafe, minimarket, atau bisnis online.

---

## 🛠️ Tech Stack
- **Framework:** Laravel 11
- **UI & Styling:** Bootstrap 4 & Tailwind CSS 4
- **Database:** MySQL / MariaDB (`pos_laravel`)
- **Icon & UI Components:** FontAwesome 6, SweetAlert2, Plus Jakarta Sans font
- **Visualisasi & Charts:** Chart.js (Grafik Traffic & Top Trending)
- **Generator Dokumen:**
  - `barryvdh/laravel-dompdf` (Cetak PDF Struk & Laporan)
  - `maatwebsite/excel` (Export XLSX Laba Rugi)
  - `qrious` (QR Generator Dinamis QRIS & Binance Pay)

---

## 🚀 Kredensial Akun Bawaan (Default Login)

| Role | Username / Email | Password | Hak Akses |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin` / `admin@pos.test` | `password` | **Full Access** ke seluruh modul, dashboard, produk, laba rugi, keuangan, pengguna, dan pengaturan toko. |
| **Kasir 1** | `kasir` / `kasir@pos.test` | `password` | Khusus Terminal Kasir POS, Cetak Struk, Riwayat Kasir, Cetak PDF Penjualan, dan **Edit Profil Pribadi**. Dilarang hapus & edit Administrator atau kasir lain. |
| **Kasir 2** | `kasir2` / `kasir2@pos.test` | `password` | Akun kasir kedua untuk pengujian multi-kasir. |

---

## ✨ Fitur-Fitur Lengkap Sesuai Permintaan

### 1. Dashboard Admin
- **Total Produk:** Menghitung jumlah seluruh katalog produk dan indikator stok menipis.
- **Total Penjualan:** Akumulasi omset rupiah dari transaksi berstatus lunas dan jumlah transaksi.
- **Total Laba Kotor:** Perhitungan otomatis `(Harga Jual - Harga Modal/Beli) * Kuantitas`.
- **Traffic Penjualan:** Grafik interaktif Chart.js 7 hari terakhir yang menampilkan pergerakan omset dan laba harian.
- **Grafik Top Trending Produk:** Diagram batang produk paling laris terjual berdasarkan kuantitas.
- **Tabel Transaksi Terbaru & Peringatan Stok Menipis.**

### 2. Produk (CRUD + Pop Up Feedback SweetAlert2)
- **Read Produk:** Tabel data produk lengkap dengan foto, kode SKU, barcode, harga modal, harga jual, margin laba, status stok (badge dinamis), filter kategori, dan pencarian cepat.
- **Tambah Produk:** Form modal popup dengan validasi + notifikasi **SweetAlert2 Berhasil / Gagal**.
- **Edit Produk:** Form modal edit data produk + notifikasi **SweetAlert2 Berhasil / Gagal**.
- **Hapus Produk:** Konfirmasi keamanan SweetAlert2 sebelum menghapus + notifikasi **SweetAlert2 Berhasil / Gagal**.

### 3. Kasir (POS Terminal)
- **Multi-Gateway Pembayaran:**
  1. **QRIS:** Generator QR Code dinamis berbasis EMVCo standar nasional (support GoPay, OVO, Dana, ShopeePay, BCA, m-Banking), dilengkapi tombol simulasi verifikasi otomatis.
  2. **Binance Pay:** Pembayaran cryptocurrency USDT jaringan BSC (BEP20) dengan konversi otomatis dari Rupiah ke USDT (kurs Rp 16.000), QR code, wallet address, dan input TXID transaksi.
  3. **BCA Virtual Account:** Nomor Virtual Account BCA otomatis (80777...) dengan fitur salin 1-klik dan verifikasi instan.
  4. **Tunai (Cash):** Tombol uang cepat (10k, 20k, 50k, 100k, uang pas) dan kalkulator kembalian otomatis.
- **Cetak Struk Pembayaran:** Tampilan struk thermal printer (58mm/80mm) yang rapi, auto-trigger `window.print()`, lengkap dengan nama toko, kasir, invoice, detail produk, subtotal, diskon, metode bayar, dan footer toko.
- **Cetak PDF Keseluruhan Total Penjualan:** Tombol download laporan PDF berisi rekapitulasi seluruh transaksi penjualan, rincian per gateway, kuantitas item terjual, dan grand total.

### 4. Laba Bersih & Rugi
- **Total Laba Bersih:** Hasil dari `Laba Kotor - Beban Operasional Toko`.
- **Total Laba Rugi:** Laporan format resmi Income Statement standar akuntansi dengan status SURPLUS LABA / RUGI.
- **Beban Operasional:** Modul pencatatan pengeluaran operasional (listrik, sewa ruko, internet, gaji, dll).
- **Cetak PDF Total Keseluruhan:** Download dokumen PDF resmi Laporan Laba Bersih & Rugi.
- **Export ke Excel (.xlsx):** Download lembar kerja Excel laporan laba rugi lengkap dengan rincian akun finansial.

### 5. Manajemen User & Hak Akses
- **Administrator Full Access:** Memiliki akses ke Dashboard, Produk, Laba Rugi, Beban, Pengguna, dan Pengaturan Toko.
- **Aturan Kasir:**
  - Kasir **DILARANG** menghapus dan mengedit akun Administrator.
  - Kasir **BISA** mengedit profil pribadi (nama, email, username, telepon, foto avatar, password) melalui menu `/profile`.
  - Kasir **DILARANG** mengedit atau menghapus akun kasir lain.
- **Login & Logout:** Redirection otomatis sesuai role (Admin ke Dashboard, Kasir langsung ke Terminal POS Kasir).

---

## 🌐 Cara Menjalankan & Mengakses Online di Jaringan Internet

### A. Menjalankan di Komputer Lokal & Jaringan Wi-Fi (LAN)
1. Cukup klik ganda (double-click) file:
   ```cmd
   jalankan-pos.bat
   ```
2. Aplikasi akan otomatis membuka browser di:
   - Komputer Utama: `http://localhost:8000`
   - Perangkat Lain di Wi-Fi yang sama (Tablet Kasir / HP / Laptop): `http://192.168.18.8:8000`

### B. Menghubungkan ke Jaringan Internet Online (Public URL)
Agar kasir atau toko cabang bisa mengakses dari jaringan internet luar tanpa batasan lokasi:
1. Pastikan server Laravel sedang berjalan (melalui `jalankan-pos.bat`).
2. Klik ganda file:
   ```cmd
   online-tunnel.bat
   ```
3. Anda akan mendapatkan URL publik HTTPS instan (misal: `https://xxxx.a.pinggy.link`) yang dapat dibuka langsung dari perangkat mana pun di seluruh dunia.
