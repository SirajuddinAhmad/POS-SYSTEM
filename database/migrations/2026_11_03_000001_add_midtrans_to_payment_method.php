<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        // PostgreSQL: lepas constraint enum lama, lalu ubah ke VARCHAR.
        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE transactions DROP CONSTRAINT IF EXISTS transactions_payment_method_check');
            DB::statement('ALTER TABLE transactions ALTER COLUMN payment_method TYPE VARCHAR(50)');
            DB::statement("ALTER TABLE transactions ALTER COLUMN payment_method SET DEFAULT 'cash'");

            return;
        }

        // SQLite (dipakai saat testing): rebuild kolom lewat Schema::change().
        if ($driver === 'sqlite') {
            Schema::table('transactions', function (Blueprint $table) {
                $table->string('payment_method', 50)->default('cash')->change();
            });

            return;
        }

        // MySQL / MariaDB
        DB::statement("ALTER TABLE transactions MODIFY COLUMN payment_method VARCHAR(50) NOT NULL DEFAULT 'cash'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE transactions ALTER COLUMN payment_method TYPE VARCHAR(50)');
            DB::statement("ALTER TABLE transactions ALTER COLUMN payment_method SET DEFAULT 'cash'");
            DB::statement("ALTER TABLE transactions ADD CONSTRAINT transactions_payment_method_check CHECK (payment_method IN ('qris', 'binance', 'bca', 'cash'))");

            return;
        }

        if ($driver === 'sqlite') {
            Schema::table('transactions', function (Blueprint $table) {
                $table->enum('payment_method', ['qris', 'binance', 'bca', 'cash'])->default('cash')->change();
            });

            return;
        }

        DB::statement("ALTER TABLE transactions MODIFY COLUMN payment_method ENUM('qris', 'binance', 'bca', 'cash') NOT NULL DEFAULT 'cash'");
    }
};
