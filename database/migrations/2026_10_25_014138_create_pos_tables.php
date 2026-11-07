<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable()->default('fa-box');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code')->unique();
            $table->string('barcode')->nullable()->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('cost_price', 14, 2)->default(0); // Harga modal/beli
            $table->decimal('selling_price', 14, 2)->default(0); // Harga jual
            $table->integer('stock')->default(0);
            $table->integer('min_stock')->default(5);
            $table->string('unit')->default('pcs');
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->date('expense_date');
            $table->string('category'); // e.g. Operasional, Listrik, Sewa, Gaji, dll
            $table->string('title');
            $table->decimal('amount', 14, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name')->default('Pelanggan Umum');
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('tax', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->enum('payment_method', ['qris', 'binance', 'bca', 'cash'])->default('cash');
            $table->enum('payment_status', ['paid', 'pending', 'cancelled'])->default('paid');
            $table->string('payment_reference')->nullable();
            $table->decimal('amount_paid', 14, 2)->default(0);
            $table->decimal('change_amount', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_code')->nullable();
            $table->string('product_name');
            $table->decimal('cost_price', 14, 2)->default(0);
            $table->decimal('selling_price', 14, 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('profit', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('settings');
    }
};
