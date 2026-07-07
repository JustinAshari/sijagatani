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
        Schema::table('penggilingan', function (Blueprint $table) {
            $table->string('komoditas')->nullable()->after('nama_penggilingan');
            $table->foreignId('provinsi_id')->nullable()->after('lokasi_makloon')->constrained('provinsi')->onDelete('set null');
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
        Schema::table('penggilingan', function (Blueprint $table) {
            $table->dropForeign(['provinsi_id']);
            $table->dropForeign(['kabupaten_id']);
            $table->dropForeign(['kecamatan_id']);
            $table->dropForeign(['kalurahan_id']);
            $table->dropColumn(['komoditas', 'provinsi_id', 'kabupaten_id', 'kecamatan_id', 'kalurahan_id']);
        });
    }
};
