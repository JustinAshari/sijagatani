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
        // Only add columns if they don't already exist to avoid errors if partial migration occurred
        Schema::table('transaksi_petani', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksi_petani', 'bank')) {
                $table->string('bank')->nullable()->after('nominal');
            }
            if (!Schema::hasColumn('transaksi_petani', 'no_rekening')) {
                $table->string('no_rekening')->nullable()->after('bank');
            }
        });

        // Convert enum column to VARCHAR temporarily so we can update values safely
        if (config('database.default') === 'mysql') {
            DB::statement("ALTER TABLE transaksi_petani MODIFY COLUMN status_transaksi VARCHAR(50) NOT NULL");
        }

        // Update existing data to match the new values
        DB::statement("UPDATE transaksi_petani SET status_transaksi = 'sudah' WHERE status_transaksi = 'sukses'");
        DB::statement("UPDATE transaksi_petani SET status_transaksi = 'belum' WHERE status_transaksi IN ('pending', 'gagal')");

        if (config('database.default') === 'mysql') {
            DB::statement("ALTER TABLE transaksi_petani MODIFY COLUMN status_transaksi ENUM('belum', 'sudah') DEFAULT 'belum' NOT NULL");
        } else {
            Schema::table('transaksi_petani', function (Blueprint $table) {
                $table->string('status_transaksi')->default('belum')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (config('database.default') === 'mysql') {
            DB::statement("ALTER TABLE transaksi_petani MODIFY COLUMN status_transaksi VARCHAR(50) NOT NULL");
            DB::statement("UPDATE transaksi_petani SET status_transaksi = 'sukses' WHERE status_transaksi = 'sudah'");
            DB::statement("UPDATE transaksi_petani SET status_transaksi = 'pending' WHERE status_transaksi = 'belum'");
            DB::statement("ALTER TABLE transaksi_petani MODIFY COLUMN status_transaksi ENUM('pending', 'sukses', 'gagal') DEFAULT 'pending' NOT NULL");
        } else {
            Schema::table('transaksi_petani', function (Blueprint $table) {
                $table->string('status_transaksi')->default('pending')->change();
            });
        }

        Schema::table('transaksi_petani', function (Blueprint $table) {
            $table->dropColumn(['bank', 'no_rekening']);
        });
    }
};
