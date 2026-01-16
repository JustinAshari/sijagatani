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
        Schema::create('penggilingan', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('petani_id')->constrained('petani')->onDelete('cascade');
            $table->decimal('jumlah_gabah', 10, 2); // in KG
            $table->decimal('hasil_beras', 10, 2)->nullable(); // in KG
            $table->decimal('biaya_giling', 10, 2)->nullable();
            $table->decimal('harga_per_kg', 10, 2)->nullable();
            $table->decimal('total_pembayaran', 10, 2)->nullable();
            $table->enum('status', ['Pending', 'Proses', 'Selesai', 'Dibayar'])->default('Pending');
            $table->text('keterangan')->nullable();
            $table->string('foto_proses')->nullable();
            $table->string('bukti_bayar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penggilingan');
    }
};
