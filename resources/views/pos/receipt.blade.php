<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $transaction->invoice_number }} - {{ $storeName }}</title>
    <!-- FontAwesome for Print toolbar icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @php
        $size = $paperSize ?? '80mm';
        $is58mm = ($size === '58mm');
    @endphp
    <style>
        @page {
            size: {{ $is58mm ? '58mm' : '80mm' }} auto;
            margin: 0;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', Courier, monospace, 'Lucida Console', Monaco;
            width: {{ $is58mm ? '54mm' : '76mm' }};
            margin: 0 auto;
            padding: {{ $is58mm ? '8px 4px 35px 4px' : '14px 8px 45px 8px' }};
            font-size: {{ $is58mm ? '10px' : '11.5px' }};
            color: #000;
            background: #fff;
            line-height: 1.3;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        
        .dashed { 
            border-bottom: 1px dashed #000; 
            margin: {{ $is58mm ? '4px 0' : '6px 0' }}; 
        }
        .double-line { 
            border-bottom: 2px solid #000; 
            margin: {{ $is58mm ? '5px 0' : '7px 0' }}; 
        }
        
        .item-table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        .item-table td { 
            padding: 1.5px 0; 
            vertical-align: top;
        }

        /* Toolbar Kontrol Cetak (Hanya tampil di layar) */
        .no-print-bar {
            background: #0f172a;
            color: #fff;
            padding: 10px 14px;
            margin: -14px -8px 14px -8px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 12px;
        }
        .btn-toolbar {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #2563eb;
            color: #fff;
            padding: 7px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 12px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-toolbar:hover {
            background: #1d4ed8;
            color: #fff;
        }
        .btn-size {
            background: #334155;
            color: #cbd5e1;
            padding: 5px 9px;
            border: 1px solid #475569;
            border-radius: 5px;
            cursor: pointer;
            font-size: 11px;
            text-decoration: none;
        }
        .btn-size.active {
            background: #10b981;
            color: #fff;
            border-color: #10b981;
            font-weight: bold;
        }
        .btn-close-tab {
            background: #475569;
            color: #fff;
            padding: 7px 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
        }

        /* Feed Paper Spacer untuk Cutter Thermal Printer */
        .paper-feed-spacer {
            height: 25px;
            display: block;
        }

        @media print {
            .no-print, .no-print-bar { 
                display: none !important; 
            }
            body { 
                padding: {{ $is58mm ? '2px 0 25px 0' : '4px 0 35px 0' }}; 
                width: 100% !important; 
                margin: 0 !important;
                background: transparent !important;
            }
        }
    </style>
</head>
<body>

    <!-- Bar Kontrol Printer (Tersembunyi otomatis saat dicetak) -->
    <div class="no-print-bar">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <div style="font-weight: bold; font-size: 13px;">
                <i class="fa-solid fa-print" style="color: #60a5fa; margin-right: 4px;"></i> Cetak Struk Kasir
            </div>
            <button class="btn-close-tab" onclick="window.close()" title="Tutup Jendela">
                <i class="fa-solid fa-xmark"></i> Tutup
            </button>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 6px;">
            <div style="display: flex; gap: 4px; align-items: center;">
                <span style="font-size: 11px; color: #94a3b8;">Kertas:</span>
                <a href="{{ request()->fullUrlWithQuery(['paper_size' => '80mm']) }}" class="btn-size {{ !$is58mm ? 'active' : '' }}">
                    80mm (Standar)
                </a>
                <a href="{{ request()->fullUrlWithQuery(['paper_size' => '58mm']) }}" class="btn-size {{ $is58mm ? 'active' : '' }}">
                    58mm (Mini)
                </a>
            </div>

            <button class="btn-toolbar" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Cetak ke Printer
            </button>
        </div>
    </div>

    <!-- Konten Struk Kasir -->
    <div class="text-center">
        <div class="bold uppercase" style="font-size: {{ $is58mm ? '12px' : '14px' }}; margin-bottom: 2px;">{{ $storeName }}</div>
        <div>{{ $storeAddress }}</div>
        <div>Telp: {{ $storePhone }}</div>
    </div>

    <div class="double-line"></div>

    <table class="item-table">
        <tr>
            <td>No: <strong>{{ $transaction->invoice_number }}</strong></td>
            <td class="text-right">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Kasir: {{ $transaction->user->name ?? 'Kasir' }}</td>
            <td class="text-right">Plg: {{ $transaction->customer_name }}</td>
        </tr>
        <tr>
            <td colspan="2">
                Metode: <strong>{{ strtoupper($transaction->payment_method) }}</strong> 
                @if($transaction->payment_reference)
                    <span style="font-size: 9px;">({{ $transaction->payment_reference }})</span>
                @endif
            </td>
        </tr>
    </table>

    <div class="dashed"></div>

    <table class="item-table">
        @foreach($transaction->items as $item)
        <tr>
            <td colspan="2" class="bold">{{ $item->product_name }}</td>
        </tr>
        <tr>
            <td style="padding-left: {{ $is58mm ? '4px' : '8px' }};">
                {{ $item->quantity }} x {{ number_format($item->selling_price, 0, ',', '.') }}
            </td>
            <td class="text-right bold">
                {{ number_format($item->subtotal, 0, ',', '.') }}
            </td>
        </tr>
        @endforeach
    </table>

    <div class="dashed"></div>

    <table class="item-table">
        <tr>
            <td>Subtotal:</td>
            <td class="text-right">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
        </tr>
        @if($transaction->discount > 0)
        <tr>
            <td>Diskon:</td>
            <td class="text-right">-Rp {{ number_format($transaction->discount, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="bold" style="font-size: {{ $is58mm ? '11px' : '13px' }};">
            <td>TOTAL:</td>
            <td class="text-right">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Bayar ({{ strtoupper($transaction->payment_method) }}):</td>
            <td class="text-right">Rp {{ number_format($transaction->amount_paid, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Kembali:</td>
            <td class="text-right">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="double-line"></div>

    <div class="text-center" style="font-size: {{ $is58mm ? '9px' : '10px' }};">
        <p style="margin: 4px 0;">{{ $storeFooter }}</p>
        <div class="bold" style="margin-top: 5px; font-size: {{ $is58mm ? '10px' : '11px' }};">*** STATUS: LUNAS ***</div>
        <div style="font-size: 8.5px; color: #555; margin-top: 3px;">POS PRO Official Thermal Receipt</div>
    </div>

    <!-- Spacer feed kertas thermal agar tidak terpotong pisau printer -->
    <div class="paper-feed-spacer"></div>

    @if($autoPrint ?? true)
    <script>
        // Otomatis membuka printer dialog setelah halaman siap
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 300);
        });
    </script>
    @endif

</body>
</html>
