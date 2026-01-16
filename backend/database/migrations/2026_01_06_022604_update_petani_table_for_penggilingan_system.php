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
        Schema::table('petani', function (Blueprint $table) {
            // Remove old fields that are no longer needed
            $table->dropColumn(['tanggal_lahir', 'jenis_kelamin']);
            
            // Add new fields for penggilingan system
            $table->string('bank')->nullable()->after('no_telepon');
            $table->string('no_rekening')->nullable()->after('bank');
            $table->decimal('luas_lahan', 10, 2)->after('alamat'); // in hectares
            $table->text('alamat_lahan')->after('luas_lahan');
            $table->decimal('potensi_panen', 10, 2)->after('alamat_lahan'); // in KG
            $table->enum('komoditi', ['Gabah', 'Jagung', 'Beras'])->after('potensi_panen');
            
            // Update foto field and add new photo fields
            $table->renameColumn('foto', 'foto_ktp');
            $table->string('foto_petani')->nullable()->after('foto_ktp');
            $table->string('foto_komoditi')->nullable()->after('foto_petani');
            $table->string('kwitansi_pembayaran')->nullable()->after('foto_komoditi');
            
            // Add tanggal at the beginning
            $table->date('tanggal')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('petani', function (Blueprint $table) {
            $table->dropColumn([
                'tanggal', 
                'bank', 
                'no_rekening', 
                'luas_lahan', 
                'alamat_lahan', 
                'potensi_panen', 
                'komoditi', 
                'foto_petani', 
                'foto_komoditi', 
                'kwitansi_pembayaran'
            ]);
            
            $table->renameColumn('foto_ktp', 'foto');
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
        });
    }
};
