<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Expense;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users
        $admin = User::create([
            'name' => 'Administrator POS',
            'username' => 'admin',
            'email' => 'admin@pos.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
            'status' => 'active',
        ]);

        $cashier1 = User::create([
            'name' => 'Budi Santoso',
            'username' => 'kasir',
            'email' => 'kasir@pos.test',
            'password' => Hash::make('password'),
            'role' => 'cashier',
            'phone' => '081298765432',
            'status' => 'active',
        ]);

        $cashier2 = User::create([
            'name' => 'Siti Aminah',
            'username' => 'kasir2',
            'email' => 'kasir2@pos.test',
            'password' => Hash::make('password'),
            'role' => 'cashier',
            'phone' => '081345678901',
            'status' => 'active',
        ]);

        // 2. Settings
        Setting::set('store_name', 'POS PRO INDONESIA');
        Setting::set('store_address', 'Jl. Sudirman Mega Mall Lt. 1 No. 45, Jakarta Pusat');
        Setting::set('store_phone', '0812-9988-7766');
        Setting::set('store_email', 'support@pospro.id');
        Setting::set('store_footer', 'Terima kasih telah berbelanja! Simpan struk ini sebagai bukti pembayaran yang sah.');
        Setting::set('qris_string', '00020101021226580014ID.CO.QRIS.WWW01189360099900000000015204541153033605802ID5916POS PRO INDONESIA6007JAKARTA61051022062070703A016304E8A2');
        Setting::set('bca_va_account', '8077708129876543');
        Setting::set('binance_pay_id', '584920193');
        Setting::set('binance_wallet', '0x39F14A5722E302194D1812E8B907572765A7C5F9');
        Setting::set('binance_network', 'BSC (BEP20)');

        // 3. Categories
        $catBeverages = Category::create(['name' => 'Minuman', 'slug' => 'minuman', 'icon' => 'fa-mug-hot']);
        $catFood = Category::create(['name' => 'Makanan & Snack', 'slug' => 'makanan-snack', 'icon' => 'fa-utensils']);
        $catGroceries = Category::create(['name' => 'Sembako & Kebutuhan', 'slug' => 'sembako', 'icon' => 'fa-shopping-basket']);
        $catStationery = Category::create(['name' => 'Alat Tulis & Kantor', 'slug' => 'atk', 'icon' => 'fa-pen-nib']);

        // 4. Products
        $productsData = [
            [
                'category_id' => $catBeverages->id,
                'code' => 'PRD-001',
                'barcode' => '899276111101',
                'name' => 'Kopi Susu Gula Aren 250ml',
                'description' => 'Espresso dengan perpaduan susu segar dan sirup gula aren asli.',
                'cost_price' => 8500,
                'selling_price' => 16000,
                'stock' => 50,
                'min_stock' => 10,
                'unit' => 'botol',
            ],
            [
                'category_id' => $catBeverages->id,
                'code' => 'PRD-002',
                'barcode' => '899276111102',
                'name' => 'Teh Botol Sosro 350ml',
                'description' => 'Minuman teh melati asli Indonesia yang menyegarkan.',
                'cost_price' => 3200,
                'selling_price' => 5000,
                'stock' => 120,
                'min_stock' => 15,
                'unit' => 'botol',
            ],
            [
                'category_id' => $catBeverages->id,
                'code' => 'PRD-003',
                'barcode' => '899276111103',
                'name' => 'Air Mineral Aqua 600ml',
                'description' => 'Air mineral pegunungan alami higienis.',
                'cost_price' => 2100,
                'selling_price' => 3500,
                'stock' => 200,
                'min_stock' => 24,
                'unit' => 'botol',
            ],
            [
                'category_id' => $catFood->id,
                'code' => 'PRD-004',
                'barcode' => '899276111104',
                'name' => 'Indomie Goreng Spesial 85g',
                'description' => 'Mie instan goreng nomor 1 di Indonesia dengan bumbu gurih.',
                'cost_price' => 2700,
                'selling_price' => 3800,
                'stock' => 150,
                'min_stock' => 20,
                'unit' => 'pcs',
            ],
            [
                'category_id' => $catFood->id,
                'code' => 'PRD-005',
                'barcode' => '899276111105',
                'name' => 'Chitato Sapi Panggang 68g',
                'description' => 'Keripik kentang bergelombang rasa sapi panggang mantap.',
                'cost_price' => 8500,
                'selling_price' => 12000,
                'stock' => 45,
                'min_stock' => 10,
                'unit' => 'bungkus',
            ],
            [
                'category_id' => $catFood->id,
                'code' => 'PRD-006',
                'barcode' => '899276111106',
                'name' => 'Roti Tawar Gandum Kupas',
                'description' => 'Roti tawar gandum kaya serat dan lezat untuk sarapan.',
                'cost_price' => 14000,
                'selling_price' => 19500,
                'stock' => 25,
                'min_stock' => 5,
                'unit' => 'bungkus',
            ],
            [
                'category_id' => $catGroceries->id,
                'code' => 'PRD-007',
                'barcode' => '899276111107',
                'name' => 'Minyak Goreng Bimoli 2 Liter',
                'description' => 'Minyak goreng kelapa sawit murni dan higienis.',
                'cost_price' => 31000,
                'selling_price' => 37000,
                'stock' => 60,
                'min_stock' => 10,
                'unit' => 'pouch',
            ],
            [
                'category_id' => $catGroceries->id,
                'code' => 'PRD-008',
                'barcode' => '899276111108',
                'name' => 'Beras Pandan Wangi 5kg',
                'description' => 'Beras pulen beraroma pandan alami pilihan nusantara.',
                'cost_price' => 68000,
                'selling_price' => 78000,
                'stock' => 30,
                'min_stock' => 5,
                'unit' => 'karung',
            ],
            [
                'category_id' => $catGroceries->id,
                'code' => 'PRD-009',
                'barcode' => '899276111109',
                'name' => 'Gula Pasir Gulaku 1kg',
                'description' => 'Gula tebu murni kristal putih manis alami.',
                'cost_price' => 14500,
                'selling_price' => 17500,
                'stock' => 80,
                'min_stock' => 12,
                'unit' => 'bungkus',
            ],
            [
                'category_id' => $catStationery->id,
                'code' => 'PRD-010',
                'barcode' => '899276111110',
                'name' => 'Buku Tulis Sinar Dunia 58 Lembar',
                'description' => 'Buku tulis kertas putih tebal dan garis rapi.',
                'cost_price' => 3500,
                'selling_price' => 5500,
                'stock' => 90,
                'min_stock' => 15,
                'unit' => 'buku',
            ],
            [
                'category_id' => $catStationery->id,
                'code' => 'PRD-011',
                'barcode' => '899276111111',
                'name' => 'Pulpen Gel Faster 0.5mm Hitam',
                'description' => 'Tinta gel lancar tidak mudah bocor.',
                'cost_price' => 2500,
                'selling_price' => 4500,
                'stock' => 140,
                'min_stock' => 20,
                'unit' => 'pcs',
            ],
        ];

        $createdProducts = [];
        foreach ($productsData as $data) {
            $createdProducts[] = Product::create($data);
        }

        // 5. Sample Past Transactions across the last 7 days to give vibrant graphs
        $methods = ['qris', 'bca', 'binance', 'cash'];
        $customers = ['Pelanggan Umum', 'Ahmad Dani', 'Rina Marlina', 'Dewi Lestari', 'Hendra Wijaya', 'Siska Amelia'];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->setHour(rand(8, 20))->setMinute(rand(0, 59));
            $txCount = rand(3, 7);

            for ($j = 1; $j <= $txCount; $j++) {
                $inv = 'INV-' . $date->format('Ymd') . '-' . str_pad($j + ($i * 10), 4, '0', STR_PAD_LEFT);
                $method = $methods[array_rand($methods)];
                $customer = $customers[array_rand($customers)];

                // Pick 1 to 4 random products
                $itemsNum = rand(1, 4);
                $shuffledProducts = collect($createdProducts)->shuffle()->take($itemsNum);

                $subtotal = 0;
                $itemsToInsert = [];

                foreach ($shuffledProducts as $prod) {
                    $qty = rand(1, 5);
                    $itemSubtotal = $prod->selling_price * $qty;
                    $itemProfit = ($prod->selling_price - $prod->cost_price) * $qty;
                    $subtotal += $itemSubtotal;

                    $itemsToInsert[] = [
                        'product_id' => $prod->id,
                        'product_code' => $prod->code,
                        'product_name' => $prod->name,
                        'cost_price' => $prod->cost_price,
                        'selling_price' => $prod->selling_price,
                        'quantity' => $qty,
                        'subtotal' => $itemSubtotal,
                        'profit' => $itemProfit,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ];
                }

                $discount = ($subtotal > 100000 && rand(0, 1)) ? 10000 : 0;
                $tax = 0; // optional
                $total = $subtotal - $discount + $tax;

                $amountPaid = $total;
                $change = 0;
                if ($method === 'cash') {
                    $amountPaid = ceil($total / 50000) * 50000;
                    if ($amountPaid < $total) {
                        $amountPaid = $total + 10000;
                    }
                    $change = $amountPaid - $total;
                }

                $tx = Transaction::create([
                    'invoice_number' => $inv,
                    'user_id' => (rand(0, 1) ? $cashier1->id : $cashier2->id),
                    'customer_name' => $customer,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'tax' => $tax,
                    'total_amount' => $total,
                    'payment_method' => $method,
                    'payment_status' => 'paid',
                    'payment_reference' => ($method === 'binance' ? 'BINANCE-TX-' . strtoupper(substr(md5(uniqid()), 0, 10)) : ($method === 'qris' ? 'QRIS-' . date('His') . rand(100, 999) : ($method === 'bca' ? 'BCA-VA-' . rand(100000, 999999) : null))),
                    'amount_paid' => $amountPaid,
                    'change_amount' => $change,
                    'notes' => 'Pembayaran lunas via ' . strtoupper($method),
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                foreach ($itemsToInsert as $item) {
                    $item['transaction_id'] = $tx->id;
                    TransactionItem::create($item);
                }
            }
        }

        // 6. Sample Expenses (Beban Operasional) for Laba Bersih calculation
        $expenses = [
            [
                'user_id' => $admin->id,
                'expense_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'category' => 'Listrik & Air',
                'title' => 'Tagihan Listrik Toko Periode Ini',
                'amount' => 450000,
                'notes' => 'Pembayaran PLN Pascabayar',
            ],
            [
                'user_id' => $admin->id,
                'expense_date' => Carbon::now()->subDays(3)->format('Y-m-d'),
                'category' => 'Internet & Komunikasi',
                'title' => 'Langganan Wi-Fi Indihome Toko',
                'amount' => 385000,
                'notes' => 'Kecepatan 50 Mbps untuk transaksi online POS',
            ],
            [
                'user_id' => $admin->id,
                'expense_date' => Carbon::now()->subDays(1)->format('Y-m-d'),
                'category' => 'Operasional & Kebersihan',
                'title' => 'Plastik Belanja & Perlengkapan Kasir',
                'amount' => 120000,
                'notes' => 'Kertas thermal struk dan kantong belanja',
            ],
        ];

        foreach ($expenses as $exp) {
            Expense::create($exp);
        }
    }
}
