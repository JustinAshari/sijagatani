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
        Schema::table('transaksi_petani', function (Blueprint $table) {
            $table->string('komoditas')->nullable()->after('petani_id');
            $table->decimal('harga_per_kg', 15, 2)->default(0)->after('volume_kg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_petani', function (Blueprint $table) {
            $table->dropColumn(['komoditas', 'harga_per_kg']);
        });
    }
};
