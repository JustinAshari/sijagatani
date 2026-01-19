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
            // Drop foreign key constraint first
            $table->dropForeign(['petani_id']);
            
            // Change petani_id to nama_petani (string)
            $table->dropColumn('petani_id');
            $table->string('nama_petani', 255)->after('tanggal_pengajuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penggilingan', function (Blueprint $table) {
            // Revert back to petani_id foreign key
            $table->dropColumn('nama_petani');
            $table->foreignId('petani_id')->after('tanggal_pengajuan')->constrained('petani')->onDelete('cascade');
        });
    }
};
