<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Transaction;

class PosSystemTest extends TestCase
{
    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('POS PRO');
    }

    public function test_admin_can_login_and_reach_dashboard()
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->post('/login', [
            'login' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_cashier_redirected_to_pos_upon_login()
    {
        $cashier = User::where('role', 'cashier')->first();

        $response = $this->post('/login', [
            'login' => $cashier->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('pos.index'));
    }

    public function test_cashier_cannot_access_admin_dashboard()
    {
        $cashier = User::where('role', 'cashier')->first();

        $response = $this->actingAs($cashier)->get('/admin/dashboard');
        $response->assertRedirect(route('pos.index'));
    }

    public function test_cashier_cannot_edit_administrator()
    {
        $cashier = User::where('role', 'cashier')->first();
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($cashier)->get(route('users.edit', $admin->id));
        $response->assertRedirect(route('pos.index'));
    }

    public function test_cashier_can_edit_own_profile()
    {
        $cashier = User::where('role', 'cashier')->first();

        $response = $this->actingAs($cashier)->get(route('profile'));
        $response->assertStatus(200);
        $response->assertSee($cashier->name);
    }

    public function test_admin_dashboard_metrics_and_charts()
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('1. Total Produk');
        $response->assertSee('2. Total Penjualan');
        $response->assertSee('3. Total Laba Kotor');
        $response->assertSee('4. Traffic Penjualan');
        $response->assertSee('5. Grafik Top Trending Produk');
    }

    public function test_product_crud_with_popups()
    {
        $admin = User::where('role', 'admin')->first();
        $category = Category::first();
        $uniqueSku = 'SKU-' . rand(10000, 99999);

        // 3. Tambah Produk + pop up berhasil
        $storeResponse = $this->actingAs($admin)->postJson(route('products.store'), [
            'code' => $uniqueSku,
            'barcode' => '899' . rand(100000000, 999999999),
            'name' => 'Produk Uji Coba Baru',
            'category_id' => $category->id,
            'cost_price' => 5000,
            'selling_price' => 8500,
            'stock' => 100,
            'min_stock' => 5,
            'unit' => 'pcs',
            'is_active' => true,
        ]);

        $storeResponse->assertStatus(200);
        $storeResponse->assertJsonFragment(['status' => 'success']);

        $createdProduct = Product::where('code', $uniqueSku)->first();
        $this->assertNotNull($createdProduct);

        // 2. Edit Produk + pop up berhasil
        $updateResponse = $this->actingAs($admin)->putJson(route('products.update', $createdProduct->id), [
            'code' => $uniqueSku,
            'barcode' => $createdProduct->barcode,
            'name' => 'Produk Uji Coba Diperbarui',
            'category_id' => $category->id,
            'cost_price' => 6000,
            'selling_price' => 10000,
            'stock' => 95,
            'min_stock' => 5,
            'unit' => 'pcs',
            'is_active' => true,
        ]);

        $updateResponse->assertStatus(200);
        $updateResponse->assertJsonFragment(['status' => 'success']);

        // 4. Hapus Produk + pop up berhasil
        $deleteResponse = $this->actingAs($admin)->deleteJson(route('products.destroy', $createdProduct->id));
        $deleteResponse->assertStatus(200);
        $deleteResponse->assertJsonFragment(['status' => 'success']);
    }

    public function test_pos_checkout_with_qris_and_stock_deduction()
    {
        $cashier = User::where('role', 'cashier')->first();
        $product = Product::first();
        $initialStock = $product->stock;

        $checkoutResponse = $this->actingAs($cashier)->postJson(route('pos.checkout'), [
            'items' => [
                [
                    'id' => $product->id,
                    'qty' => 2,
                ]
            ],
            'payment_method' => 'qris',
            'customer_name' => 'Pelanggan QRIS Test',
            'discount' => 0,
            'amount_paid' => $product->selling_price * 2,
            'payment_reference' => 'QRIS-REF-12345',
            'notes' => 'Pembayaran QRIS test'
        ]);

        $checkoutResponse->assertStatus(200);
        $checkoutResponse->assertJsonFragment(['status' => 'success']);

        // Verify stock deducted
        $this->assertEquals($initialStock - 2, $product->fresh()->stock);
    }

    public function test_profit_loss_report_and_exports()
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('reports.profit-loss'));
        $response->assertStatus(200);
        $response->assertSee('TOTAL LABA BERSIH');

        $pdfResponse = $this->actingAs($admin)->get(route('reports.profit-loss.pdf'));
        $pdfResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $pdfResponse->headers->get('content-type'));

        $excelResponse = $this->actingAs($admin)->get(route('reports.profit-loss.excel'));
        $excelResponse->assertStatus(200);
    }

    public function test_sales_pdf_export()
    {
        $cashier = User::where('role', 'cashier')->first();

        $response = $this->actingAs($cashier)->get(route('pos.export-pdf'));
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }
}
