<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Total Penjualan</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .header h2 {
            margin: 0 0 4px 0;
            color: #1e3a8a;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            color: #666;
            font-size: 10px;
        }
        .title-box {
            text-align: center;
            margin-bottom: 15px;
        }
        .title-box h3 {
            margin: 0;
            font-size: 14px;
            color: #0f172a;
        }
        .title-box span {
            font-size: 10px;
            color: #64748b;
        }
        .summary-cards {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .summary-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 8px 12px;
            text-align: center;
        }
        .summary-card .label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
        }
        .summary-card .value {
            font-size: 14px;
            font-weight: bold;
            color: #1e293b;
            margin-top: 2px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table th {
            background: #2563eb;
            color: #ffffff;
            font-size: 10px;
            text-transform: uppercase;
            padding: 6px 8px;
            text-align: left;
        }
        table.data-table td {
            border-bottom: 1px solid #e2e8f0;
            padding: 6px 8px;
            font-size: 10px;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 8px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-midtrans { background: #e0e7ff; color: #3730a3; }
        .badge-qris { background: #fef3c7; color: #92400e; }
        .badge-binance { background: #fef08a; color: #854d0e; }
        .badge-bca { background: #dbeafe; color: #1e40af; }
        .badge-cash { background: #dcfce7; color: #166534; }
        .footer {
            margin-top: 25px;
            text-align: right;
            font-size: 10px;
            color: #64748b;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>{{ $storeName }}</h2>
        <p>{{ $storeAddress }} &bull; Sistem Point of Sale Online</p>
    </div>

    <div class="title-box">
        <h3>LAPORAN KESELURUHAN TOTAL PENJUALAN</h3>
        <span>Periode: {{ $periodLabel }} &bull; Dicetak: {{ date('d/m/Y H:i') }}</span>
    </div>

    <!-- Summary Metrics -->
    <table class="summary-cards">
        <tr>
            <td class="summary-card" style="width: 25%;">
                <div class="label">Total Transaksi</div>
                <div class="value">{{ number_format($totalTransactions) }}</div>
            </td>
            <td class="summary-card" style="width: 25%;">
                <div class="label">Total Item Terjual</div>
                <div class="value">{{ number_format($totalItemsSold) }} Pcs</div>
            </td>
            <td class="summary-card" style="width: 25%;">
                <div class="label">Total Omset Penjualan</div>
                <div class="value" style="color: #059669;">Rp {{ number_format($totalOmset, 0, ',', '.') }}</div>
            </td>
            <td class="summary-card" style="width: 25%;">
                <div class="label">Total Estimasi Laba</div>
                <div class="value" style="color: #2563eb;">Rp {{ number_format($totalProfit, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <!-- Payment Breakdown Summary -->
    <div style="margin-bottom: 12px;">
        <strong style="font-size: 10px; color: #475569; text-transform: uppercase;">Rincian Pembayaran Berdasarkan Gateway:</strong>
        <div style="margin-top: 4px; font-size: 10px;">
            @foreach($byPaymentMethod as $method => $data)
                <span style="margin-right: 15px;">
                    <strong>{{ strtoupper($method) }}:</strong> {{ $data['count'] }} transaksi (Rp {{ number_format($data['total'], 0, ',', '.') }})
                </span>
            @endforeach
        </div>
    </div>

    <!-- Transactions Detailed Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">No</th>
                <th>Invoice</th>
                <th>Tanggal & Waktu</th>
                <th>Kasir</th>
                <th>Pelanggan</th>
                <th>Gateway</th>
                <th class="text-center">Item</th>
                <th class="text-right">Total Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $tx)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="bold">{{ $tx->invoice_number }}</td>
                <td>{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $tx->user->name ?? 'Kasir' }}</td>
                <td>{{ $tx->customer_name }}</td>
                <td>
                    @if($tx->payment_method === 'qris')
                        <span class="badge badge-qris">QRIS</span>
                    @elseif($tx->payment_method === 'binance')
                        <span class="badge badge-binance">BINANCE</span>
                    @elseif($tx->payment_method === 'bca')
                        <span class="badge badge-bca">BCA VA</span>
                    @else
                        <span class="badge badge-cash">CASH</span>
                    @endif
                </td>
                <td class="text-center">{{ $tx->items->sum('quantity') }}</td>
                <td class="text-right bold">Rp {{ number_format($tx->total_amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Tidak ada transaksi ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background: #e2e8f0; font-weight: bold;">
                <td colspan="7" class="text-right" style="padding: 8px;">GRAND TOTAL KESELURUHAN:</td>
                <td class="text-right" style="padding: 8px; color: #059669; font-size: 11px;">
                    Rp {{ number_format($totalOmset, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Laporan Resmi POS &bull; Dicetak oleh: {{ Auth::user()->name }} ({{ strtoupper(Auth::user()->role) }})</p>
    </div>

</body>
</html>
