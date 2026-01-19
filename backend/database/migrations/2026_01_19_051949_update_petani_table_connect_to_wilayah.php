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
            // Drop old string columns
            $table->dropColumn(['kabupaten', 'kecamatan', 'desa']);
            
            // Add foreign key columns
            $table->foreignId('provinsi_id')->nullable()->after('alamat')->constrained('provinsi')->onDelete('set null');
            $table->foreignId('kabupaten_id')->nullable()->after('provinsi_id')->constrained('kabupaten')->onDelete('set null');
            $table->foreignId('kecamatan_id')->nullable()->after('kabupaten_id')->constrained('kecamatan')->onDelete('set null');
            $table->foreignId('kalurahan_id')->nullable()->after('kecamatan_id')->constrained('kalurahan')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('petani', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['provinsi_id']);
            $table->dropForeign(['kabupaten_id']);
            $table->dropForeign(['kecamatan_id']);
            $table->dropForeign(['kalurahan_id']);
            
            // Drop columns
            $table->dropColumn(['provinsi_id', 'kabupaten_id', 'kecamatan_id', 'kalurahan_id']);
            
            // Restore old string columns
            $table->string('kabupaten')->after('alamat');
            $table->string('kecamatan')->after('kabupaten');
            $table->string('desa')->nullable()->after('kecamatan');
        });
    }
};
