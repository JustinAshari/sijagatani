<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kolom ini digunakan untuk akun dengan role 'penggilingan'
            // Menghubungkan akun dengan nama penggilingan yang boleh dikelola
            $table->string('nama_penggilingan')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nama_penggilingan');
        });
    }
};
