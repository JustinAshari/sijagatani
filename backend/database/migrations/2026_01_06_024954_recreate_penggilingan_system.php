<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old penggilingan table
        Schema::dropIfExists('penggilingan');
        
        // Create new penggilingan table with transport system
        Schema::create('penggilingan', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_pengajuan');
            $table->foreignId('petani_id')->constrained('petani')->onDelete('cascade');
            $table->string('nama_penggilingan');
            $table->string('foto_gkp_1')->nullable(); // Foto di sawah
            $table->string('foto_gkp_2')->nullable(); // Foto di penggilingan
            $table->string('lokasi_makloon');
            $table->decimal('total_tonase', 10, 3)->default(0); // Auto-calculated from transport
            $table->integer('jumlah_angkutan')->default(0); // Auto-calculated count of transport
            $table->timestamps();
        });
        
        // Create transport table (1-20 drivers per penggilingan)
        Schema::create('penggilingan_transport', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penggilingan_id')->constrained('penggilingan')->onDelete('cascade');
            $table->integer('urutan')->default(1); // 1-20
            $table->string('nama_pengemudi');
            $table->string('nopol', 20);
            $table->decimal('kuantum', 10, 3); // in tons (tonase)
            $table->string('nota_timbang')->nullable(); // Upload nota timbang file
            $table->string('surat_jalan')->nullable(); // Upload surat jalan file
            $table->timestamps();
            
            $table->index(['penggilingan_id', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penggilingan_transport');
        Schema::dropIfExists('penggilingan');
    }
};
