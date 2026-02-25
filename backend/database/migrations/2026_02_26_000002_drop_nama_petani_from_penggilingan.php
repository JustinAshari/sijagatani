<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penggilingan', function (Blueprint $table) {
            $table->dropColumn('nama_petani');
        });
    }

    public function down(): void
    {
        Schema::table('penggilingan', function (Blueprint $table) {
            $table->string('nama_petani', 255)->nullable()->after('tanggal_pengajuan');
        });
    }
};
