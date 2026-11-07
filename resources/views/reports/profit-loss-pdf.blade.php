<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Laba Bersih & Rugi</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #1e293b;
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
            color: #64748b;
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
            text-transform: uppercase;
        }
        .title-box span {
            font-size: 10px;
            color: #64748b;
        }
        table.statement-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.statement-table th {
            background: #0f172a;
            color: #ffffff;
            font-size: 10px;
            text-transform: uppercase;
            padding: 8px 10px;
            text-align: left;
        }
        table.statement-table td {
            border-bottom: 1px solid #e2e8f0;
            padding: 6px 10px;
            font-size: 10px;
        }
        .section-header {
            background-color: #f1f5f9;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            color: #334155;
            padding: 6px 10px;
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
            color: #7f1d1d;
            font-weight: bold;
            font-size: 12px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .footer {
            margin-top: 25px;
            text-align: right;
            font-size: 9px;
            color: #64748b;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>{{ $storeName }}</h2>
        <p>{{ $storeAddress }} &bull; POS Online Management System</p>
    </div>

    <div class="title-box">
        <h3>LAPORAN LABA BERSIH & RUGI (INCOME STATEMENT)</h3>
        <span>Periode: {{ $periodLabel }} &bull; Tanggal Cetak: {{ date('d/m/Y H:i:s') }}</span>
    </div>

    <table class="statement-table">
        <thead>
            <tr>
                <th colspan="2">Uraian / Akun Finansial</th>
                <th class="text-right" style="width: 160px;">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <!-- I. PENDAPATAN OPERASIONAL -->
            <tr class="section-header">
                <td colspan="3">I. PENDAPATAN PENJUALAN</td>
            </tr>
            <tr>
                <td style="padding-left: 20px;">Penjualan Kotor (Gross Sales)</td>
                <td></td>
                <td class="text-right">Rp {{ number_format($grossSales, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding-left: 20px;">Potongan / Diskon Penjualan</td>
                <td></td>
                <td class="text-right" style="color: #dc2626;">-Rp {{ number_format($totalDiscount, 0, ',', '.') }}</td>
            </tr>
            <tr class="subtotal-row">
                <td colspan="2" class="bold">TOTAL PENJUALAN BERSIH (NET REVENUE)</td>
                <td class="text-right bold">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
            </tr>

            <!-- II. HARGA POKOK PENJUALAN -->
            <tr class="section-header">
                <td colspan="3">II. HARGA POKOK PENJUALAN (HPP)</td>
            </tr>
            <tr>
                <td style="padding-left: 20px;">Biaya Modal Barang yang Terjual (Cost of Goods Sold)</td>
                <td></td>
                <td class="text-right" style="color: #dc2626;">-Rp {{ number_format($totalHpp, 0, ',', '.') }}</td>
            </tr>
            <tr class="subtotal-row" style="background: #e0e7ff; color: #1e3a8a;">
                <td colspan="2" class="bold">TOTAL LABA KOTOR (GROSS PROFIT)</td>
                <td class="text-right bold" style="font-size: 11px;">Rp {{ number_format($totalGrossProfit, 0, ',', '.') }}</td>
            </tr>

            <!-- III. BEBAN OPERASIONAL -->
            <tr class="section-header">
                <td colspan="3">III. BEBAN OPERASIONAL (OPERATING EXPENSES)</td>
            </tr>
            @forelse($expensesByCategory as $catName => $amount)
            <tr>
                <td style="padding-left: 20px;">{{ $catName }}</td>
                <td></td>
                <td class="text-right">Rp {{ number_format($amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td style="padding-left: 20px;" colspan="3">Tidak ada beban operasional yang dicatat pada periode ini.</td>
            </tr>
            @endforelse
            <tr class="subtotal-row">
                <td colspan="2" class="bold">TOTAL BEBAN OPERASIONAL</td>
                <td class="text-right bold" style="color: #dc2626;">-Rp {{ number_format($totalExpenses, 0, ',', '.') }}</td>
            </tr>

            <!-- IV. TOTAL LABA / RUGI BERSIH -->
            <tr class="{{ $isLoss ? 'net-loss-row' : 'net-profit-row' }}">
                <td colspan="2" style="padding: 10px;">
                    TOTAL LABA BERSIH (NET PROFIT / LOSS)
                    @if($isLoss)
                        <span style="color: #991b1b; font-weight: bold;">(STATUS: RUGI)</span>
                    @else
                        <span style="color: #166534; font-weight: bold;">(STATUS: SURPLUS LABA)</span>
                    @endif
                </td>
                <td class="text-right" style="padding: 10px; font-size: 13px;">
                    Rp {{ number_format($totalNetProfit, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Laporan Keuangan Resmi Point of Sale Online &bull; Dicetak oleh: {{ Auth::user()->name }}</p>
    </div>

</body>
</html>
