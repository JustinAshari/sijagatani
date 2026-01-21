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
        // First, modify the enum to add 'lapangan' while keeping 'petani'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'admin', 'petani', 'lapangan', 'penggilingan') NOT NULL DEFAULT 'petani'");
        
        // Then, update existing 'petani' role to 'lapangan'
        DB::statement("UPDATE users SET role = 'lapangan' WHERE role = 'petani'");
        
        // Finally, remove 'petani' from enum and set default to 'lapangan'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'admin', 'lapangan', 'penggilingan') NOT NULL DEFAULT 'lapangan'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, modify the enum to add 'petani' back while keeping 'lapangan'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'admin', 'petani', 'lapangan', 'penggilingan') NOT NULL DEFAULT 'lapangan'");
        
        // Then, update existing 'lapangan' role back to 'petani'
        DB::statement("UPDATE users SET role = 'petani' WHERE role = 'lapangan'");
        
        // Finally, remove 'lapangan' from enum and set default to 'petani'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'admin', 'petani', 'penggilingan') NOT NULL DEFAULT 'petani'");
    }
};
