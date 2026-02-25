<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penggilingan', function (Blueprint $table) {
            $table->enum('status_verifikasi', ['pending', 'disetujui', 'ditolak'])
                  ->default('pending')
                  ->after('jumlah_angkutan');
            $table->text('catatan_verifikasi')->nullable()->after('status_verifikasi');
            $table->timestamp('verified_at')->nullable()->after('catatan_verifikasi');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete()->after('verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('penggilingan', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['status_verifikasi', 'catatan_verifikasi', 'verified_at', 'verified_by']);
        });
    }
};
