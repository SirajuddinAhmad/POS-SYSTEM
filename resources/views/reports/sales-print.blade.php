<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Penjualan - {{ $storeName }}</title>
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

        /* KPI Cards Grid */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 18px;
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

        /* Payment Summary Pills */
        .method-summary {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 18px;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 10px;
            font-size: 10.5px;
        }
        .method-pill strong {
            color: #1e3a8a;
        }

        /* Data Table */
        table.report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        table.report-table th {
            background: #1e3a8a;
            color: #ffffff;
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 7px 8px;
            text-align: left;
            border: 1px solid #1e3a8a;
        }
        table.report-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            font-size: 10px;
        }
        table.report-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        table.report-table tfoot td {
            background-color: #f1f5f9;
            font-weight: bold;
            border-top: 2px solid #0f172a;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 8.5px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-midtrans { background: #e0e7ff; color: #3730a3; }
        .badge-qris { background: #fef3c7; color: #92400e; }
        .badge-binance { background: #fef08a; color: #854d0e; }
        .badge-bca { background: #dbeafe; color: #1e40af; }
        .badge-cash { background: #dcfce7; color: #166534; }

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
            table.report-table th {
                background: #1e3a8a !important;
                color: #fff !important;
            }
        }
    </style>
</head>
<body>

    <!-- Toolbar Cetak (Tersembunyi otomatis saat di-print) -->
    <div class="print-toolbar no-print">
        <div style="display:flex; align-items:center; gap:8px;">
            <i class="fa-solid fa-print text-blue-400"></i>
            <span style="font-weight:bold;">Pratinjau Cetak Laporan Penjualan (A4)</span>
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
            <h3>LAPORAN TOTAL PENJUALAN KASIR</h3>
            <span>Periode: {{ $periodLabel }}</span>
        </div>

        <!-- KPI Ringkasan -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-label">Total Omset Penjualan</div>
                <div class="kpi-value" style="color: #2563eb;">Rp {{ number_format($totalOmset, 0, ',', '.') }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Estimasi Total Laba</div>
                <div class="kpi-value" style="color: #16a34a;">Rp {{ number_format($totalProfit, 0, ',', '.') }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Jumlah Transaksi</div>
                <div class="kpi-value">{{ number_format($totalTransactions) }} Transaksi</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Total Item Terjual</div>
                <div class="kpi-value">{{ number_format($totalItemsSold) }} Pcs</div>
            </div>
        </div>

        <!-- Rincian Gateway Pembayaran -->
        <div class="method-summary">
            @foreach($byPaymentMethod as $method => $data)
            <div class="method-pill">
                <strong>{{ strtoupper($method) }}:</strong> {{ $data['count'] }} tx &bull; Rp {{ number_format($data['total'], 0, ',', '.') }}
            </div>
            @endforeach
        </div>

        <!-- Tabel Rincian Transaksi -->
        <table class="report-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 30px;">No</th>
                    <th>No. Invoice</th>
                    <th>Waktu</th>
                    <th>Kasir</th>
                    <th>Pelanggan</th>
                    <th class="text-center">Metode</th>
                    <th class="text-center">Items</th>
                    <th class="text-right">Total Tagihan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $index => $tx)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $tx->invoice_number }}</strong>
                        @if($tx->payment_reference)
                            <div style="font-size: 8.5px; color: #64748b;">Ref: {{ $tx->payment_reference }}</div>
                        @endif
                    </td>
                    <td>{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $tx->user->name ?? 'Kasir' }}</td>
                    <td>{{ $tx->customer_name }}</td>
                    <td class="text-center">
                        <span class="badge badge-{{ $tx->payment_method }}">
                            {{ strtoupper($tx->payment_method) }}
                        </span>
                    </td>
                    <td class="text-center">{{ $tx->items->sum('quantity') }} pcs</td>
                    <td class="text-right font-bold">
                        Rp {{ number_format($tx->total_amount, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #94a3b8;">
                        Tidak ada transaksi penjualan pada periode ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="text-right" style="padding: 8px;">TOTAL KESELURUHAN PENJUALAN:</td>
                    <td class="text-center">{{ number_format($totalItemsSold) }} pcs</td>
                    <td class="text-right" style="font-size: 11.5px; color: #1e3a8a;">
                        Rp {{ number_format($totalOmset, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Lembar Pengesahan / Tanda Tangan -->
        <div class="signature-section">
            <div class="signature-box">
                <div>Dibuat oleh,</div>
                <div style="font-size: 9.5px; color: #64748b;">Kasir Pelaksana</div>
                <div class="signature-space"></div>
                <div class="signature-name">{{ Auth::user()->name }}</div>
                <div style="font-size: 9.5px; color: #64748b;">{{ strtoupper(Auth::user()->role) }}</div>
            </div>

            <div class="signature-box">
                <div>Mengetahui & Menyetujui,</div>
                <div style="font-size: 9.5px; color: #64748b;">Pemilik / Manager Toko</div>
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
