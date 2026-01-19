<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'superadmin' to the role enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'petani', 'penggilingan', 'superadmin') NOT NULL DEFAULT 'petani'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'superadmin' from the role enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'petani', 'penggilingan') NOT NULL DEFAULT 'petani'");
    }
};
