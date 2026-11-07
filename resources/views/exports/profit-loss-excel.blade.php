<table>
    <thead>
        <tr>
            <th colspan="3" style="font-size: 16px; font-weight: bold; text-align: center;">{{ $storeName }}</th>
        </tr>
        <tr>
            <th colspan="3" style="font-size: 12px; font-weight: bold; text-align: center;">LAPORAN LABA BERSIH DAN RUGI (INCOME STATEMENT)</th>
        </tr>
        <tr>
            <th colspan="3" style="font-size: 10px; text-align: center;">Periode: {{ $periodLabel }}</th>
        </tr>
        <tr>
            <th colspan="3"></th>
        </tr>
        <tr style="background-color: #2563eb; color: #ffffff; font-weight: bold;">
            <th style="width: 40px;">Akun Finansial</th>
            <th style="width: 25px;">Catatan</th>
            <th style="width: 25px; text-align: right;">Jumlah (Rp)</th>
        </tr>
    </thead>
    <tbody>
        <!-- I. PENDAPATAN -->
        <tr style="background-color: #f1f5f9; font-weight: bold;">
            <td colspan="3">I. PENDAPATAN PENJUALAN</td>
        </tr>
        <tr>
            <td>Penjualan Kotor (Gross Sales)</td>
            <td>Subtotal transaksi</td>
            <td style="text-align: right;">{{ $grossSales }}</td>
        </tr>
        <tr>
            <td>Potongan dan Diskon Penjualan</td>
            <td>Diskon kasir</td>
            <td style="text-align: right;">-{{ $totalDiscount }}</td>
        </tr>
        <tr style="font-weight: bold; background-color: #dbeafe;">
            <td>TOTAL PENJUALAN BERSIH</td>
            <td></td>
            <td style="text-align: right;">{{ $totalRevenue }}</td>
        </tr>

        <!-- II. HPP -->
        <tr style="background-color: #f1f5f9; font-weight: bold;">
            <td colspan="3">II. HARGA POKOK PENJUALAN (HPP)</td>
        </tr>
        <tr>
            <td>Beban Pokok Modal Barang Terjual (COGS)</td>
            <td>Total modal produk</td>
            <td style="text-align: right;">-{{ $totalHpp }}</td>
        </tr>
        <tr style="font-weight: bold; background-color: #e0e7ff;">
            <td>TOTAL LABA KOTOR (GROSS PROFIT)</td>
            <td></td>
            <td style="text-align: right;">{{ $totalGrossProfit }}</td>
        </tr>

        <!-- III. BEBAN OPERASIONAL -->
        <tr style="background-color: #f1f5f9; font-weight: bold;">
            <td colspan="3">III. BEBAN OPERASIONAL</td>
        </tr>
        @foreach($expensesByCategory as $catName => $amount)
        <tr>
            <td>Beban {{ $catName }}</td>
            <td>Operasional</td>
            <td style="text-align: right;">{{ $amount }}</td>
        </tr>
        @endforeach
        <tr style="font-weight: bold; background-color: #fef3c7;">
            <td>TOTAL BEBAN OPERASIONAL</td>
            <td></td>
            <td style="text-align: right;">-{{ $totalExpenses }}</td>
        </tr>

        <!-- IV. TOTAL LABA BERSIH -->
        <tr style="font-weight: bold; font-size: 13px; background-color: {{ $isLoss ? '#fee2e2' : '#dcfce7' }};">
            <td>TOTAL LABA BERSIH (NET PROFIT / LOSS)</td>
            <td>{{ $isLoss ? 'STATUS: RUGI' : 'STATUS: SURPLUS LABA' }}</td>
            <td style="text-align: right;">{{ $totalNetProfit }}</td>
        </tr>
    </tbody>
</table>
