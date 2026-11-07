<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Laba Bersih & Rugi - {{ $storeName }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
            background: #f8fafc;
            margin: 0;
            padding: 20px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .page-container {
            max-width: 210mm;
            margin: 0 auto;
            background: #fff;
            padding: 25px 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-radius: 8px;
        }

        /* Top Action Bar (Hanya tampil di layar) */
        .print-toolbar {
            max-width: 210mm;
            margin: 0 auto 15px auto;
            background: #0f172a;
            color: #fff;
            padding: 12px 18px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .btn-print {
            background: #2563eb;
            color: #fff;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 13px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print:hover {
            background: #1d4ed8;
        }
        .btn-close {
            background: #475569;
            color: #fff;
            padding: 8px 14px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
        }

        /* Kop Laporan Resmi */
        .header-kop {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .header-brand h2 {
            margin: 0 0 4px 0;
            color: #1e3a8a;
            font-size: 20px;
            font-weight: 800;
            text-transform: uppercase;
        }
        .header-brand p {
            margin: 0;
            color: #475569;
            font-size: 11px;
        }
        .header-meta {
            text-align: right;
            font-size: 10px;
            color: #64748b;
        }

        .title-section {
            text-align: center;
            margin-bottom: 18px;
        }
        .title-section h3 {
            margin: 0 0 4px 0;
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .title-section span {
            font-size: 11px;
            color: #64748b;
            font-weight: 600;
        }

        /* 4 KPI Cards Grid */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        .kpi-card {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 10px 12px;
            border-radius: 6px;
            text-align: center;
        }
        .kpi-label {
            font-size: 9px;
            color: #64748b;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .kpi-value {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
        }

        /* Financial Statement Table */
        table.statement-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        table.statement-table th {
            background: #0f172a;
            color: #ffffff;
            font-size: 10px;
            text-transform: uppercase;
            padding: 8px 10px;
            text-align: left;
            border: 1px solid #0f172a;
        }
        table.statement-table td {
            border: 1px solid #e2e8f0;
            padding: 7px 10px;
            font-size: 10.5px;
        }
        .section-header {
            background-color: #f1f5f9;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            color: #1e293b;
            padding: 7px 10px;
        }
        .subtotal-row {
            background-color: #f8fafc;
            font-weight: bold;
        }
        .net-profit-row {
            background-color: #dcfce7;
            color: #14532d;
            font-weight: bold;
            font-size: 12px;
        }
        .net-loss-row {
            background-color: #fee2e2;
            color: #991b1b;
            font-weight: bold;
            font-size: 12px;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Tanda Tangan */
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .signature-box {
            text-align: center;
            width: 220px;
        }
        .signature-space {
            height: 60px;
        }
        .signature-name {
            font-weight: bold;
            border-bottom: 1px solid #0f172a;
            padding-bottom: 3px;
            display: inline-block;
            min-width: 160px;
        }

        @media print {
            .no-print, .print-toolbar {
                display: none !important;
            }
            body {
                background: #fff !important;
                padding: 0 !important;
            }
            .page-container {
                box-shadow: none !important;
                padding: 0 !important;
                max-width: 100% !important;
                border-radius: 0 !important;
            }
            table.statement-table th {
                background: #0f172a !important;
                color: #fff !important;
            }
            .net-profit-row {
                background-color: #dcfce7 !important;
            }
            .net-loss-row {
                background-color: #fee2e2 !important;
            }
        }
    </style>
</head>
<body>

    <!-- Toolbar Cetak (Tersembunyi otomatis saat di-print) -->
    <div class="print-toolbar no-print">
        <div style="display:flex; align-items:center; gap:8px;">
            <i class="fa-solid fa-print text-blue-400"></i>
            <span style="font-weight:bold;">Pratinjau Cetak Laporan Laba Bersih & Rugi (A4)</span>
        </div>
        <div style="display:flex; gap:8px;">
            <button class="btn-print" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Cetak ke Printer Sekarang
            </button>
            <button class="btn-close" onclick="window.close()">
                <i class="fa-solid fa-xmark"></i> Tutup
            </button>
        </div>
    </div>

    <!-- Halaman Dokumen A4 -->
    <div class="page-container">

        <!-- Header Kop Toko -->
        <div class="header-kop">
            <div class="header-brand">
                <h2>{{ $storeName }}</h2>
                <p>{{ $storeAddress }} | Telp: {{ $storePhone }}</p>
            </div>
            <div class="header-meta">
                <div>Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
                <div>Oleh: {{ Auth::user()->name }} ({{ strtoupper(Auth::user()->role) }})</div>
            </div>
        </div>

        <!-- Judul Laporan -->
        <div class="title-section">
            <h3>LAPORAN LABA BERSIH & RUGI (INCOME STATEMENT)</h3>
            <span>Periode: {{ $periodLabel }}</span>
        </div>

        <!-- KPI Ringkasan 4 Kartu -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-label">Total Penjualan Bersih</div>
                <div class="kpi-value" style="color: #2563eb;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">HPP (Modal Barang)</div>
                <div class="kpi-value" style="color: #dc2626;">Rp {{ number_format($totalHpp, 0, ',', '.') }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Total Laba Kotor</div>
                <div class="kpi-value" style="color: #4f46e5;">Rp {{ number_format($totalGrossProfit, 0, ',', '.') }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Total Laba Bersih</div>
                <div class="kpi-value" style="color: {{ $isLoss ? '#dc2626' : '#16a34a' }};">
                    Rp {{ number_format($totalNetProfit, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <!-- Tabel Laporan Laba Rugi Akuntansi -->
        <table class="statement-table">
            <thead>
                <tr>
                    <th colspan="2">Keterangan Akun Keuangan</th>
                    <th class="text-right" style="width: 160px;">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <!-- Bagian 1: PENDAPATAN OPERASIONAL -->
                <tr class="section-header">
                    <td colspan="3">I. PENDAPATAN PENJUALAN</td>
                </tr>
                <tr>
                    <td style="padding-left: 20px;">Penjualan Kotor (Subtotal Produk Terjual)</td>
                    <td></td>
                    <td class="text-right">Rp {{ number_format($grossSales, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 20px;">Potongan & Diskon Penjualan</td>
                    <td></td>
                    <td class="text-right" style="color: #dc2626;">-Rp {{ number_format($totalDiscount, 0, ',', '.') }}</td>
                </tr>
                <tr class="subtotal-row">
                    <td colspan="2" style="padding-left: 20px;">Total Penjualan Bersih (Net Sales)</td>
                    <td class="text-right" style="color: #1e3a8a;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                </tr>

                <!-- Bagian 2: HARGA POKOK PENJUALAN (HPP) -->
                <tr class="section-header">
                    <td colspan="3">II. HARGA POKOK PENJUALAN (HPP)</td>
                </tr>
                <tr>
                    <td style="padding-left: 20px;">Total Biaya Modal Produk Terjual (COGS)</td>
                    <td></td>
                    <td class="text-right" style="color: #dc2626;">-Rp {{ number_format($totalHpp, 0, ',', '.') }}</td>
                </tr>
                <tr class="subtotal-row">
                    <td colspan="2" style="padding-left: 20px;">III. TOTAL LABA KOTOR (GROSS PROFIT)</td>
                    <td class="text-right" style="color: #4f46e5; font-size: 11px;">Rp {{ number_format($totalGrossProfit, 0, ',', '.') }}</td>
                </tr>

                <!-- Bagian 3: BEBAN OPERASIONAL -->
                <tr class="section-header">
                    <td colspan="3">IV. BEBAN OPERASIONAL (EXPENSES)</td>
                </tr>
                @forelse($expensesByCategory as $category => $amount)
                <tr>
                    <td style="padding-left: 20px;">Beban {{ ucwords(str_replace('_', ' ', $category)) }}</td>
                    <td class="text-right" style="color: #64748b; font-size: 9.5px;">
                        {{ $expenses->where('category', $category)->count() }} transaksi
                    </td>
                    <td class="text-right">Rp {{ number_format($amount, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="padding-left: 20px; color: #94a3b8; font-style: italic;">
                        Tidak ada catatan pengeluaran operasional pada periode ini.
                    </td>
                </tr>
                @endforelse
                <tr class="subtotal-row">
                    <td colspan="2" style="padding-left: 20px;">Total Beban Operasional Usaha</td>
                    <td class="text-right" style="color: #dc2626;">-Rp {{ number_format($totalExpenses, 0, ',', '.') }}</td>
                </tr>

                <!-- Bagian 4: LABA BERSIH / RUGI AKHIR -->
                <tr class="{{ $isLoss ? 'net-loss-row' : 'net-profit-row' }}">
                    <td colspan="2" style="padding: 10px; font-weight: bold;">
                        V. {{ $isLoss ? 'TOTAL RUGI BERSIH (NET LOSS)' : 'TOTAL LABA BERSIH (NET PROFIT)' }}
                    </td>
                    <td class="text-right" style="padding: 10px; font-weight: bold; font-size: 12.5px;">
                        Rp {{ number_format($totalNetProfit, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Lembar Pengesahan / Tanda Tangan -->
        <div class="signature-section">
            <div class="signature-box">
                <div>Disusun Oleh,</div>
                <div style="font-size: 9.5px; color: #64748b;">Bagian Keuangan / Admin</div>
                <div class="signature-space"></div>
                <div class="signature-name">{{ Auth::user()->name }}</div>
                <div style="font-size: 9.5px; color: #64748b;">{{ strtoupper(Auth::user()->role) }}</div>
            </div>

            <div class="signature-box">
                <div>Diperiksa & Disetujui Oleh,</div>
                <div style="font-size: 9.5px; color: #64748b;">Pemilik / Direktur Usaha</div>
                <div class="signature-space"></div>
                <div class="signature-name">( ............................................ )</div>
                <div style="font-size: 9.5px; color: #64748b;">{{ $storeName }}</div>
            </div>
        </div>

    </div>

    <!-- Script auto-trigger printer dialog -->
    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 300);
        });
    </script>
</body>
</html>
