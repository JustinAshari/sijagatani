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
        // First, update existing data to use new role values
        // superadmin -> admin, user -> petani
        DB::statement("UPDATE users SET role = 'superadmin' WHERE role = 'superadmin'");
        DB::statement("UPDATE users SET role = 'user' WHERE role = 'user'");
        
        // Now modify the enum to add new values temporarily including old ones
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'user', 'admin', 'petani', 'penggilingan') NOT NULL DEFAULT 'petani'");
        
        // Update data to new role values
        DB::statement("UPDATE users SET role = 'admin' WHERE role = 'superadmin'");
        DB::statement("UPDATE users SET role = 'petani' WHERE role = 'user'");
        
        // Finally, set enum to only new values
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'petani', 'penggilingan') NOT NULL DEFAULT 'petani'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse: add old values temporarily
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'petani', 'penggilingan', 'superadmin', 'user') NOT NULL DEFAULT 'user'");
        
        // Update data back to old values
        DB::statement("UPDATE users SET role = 'superadmin' WHERE role = 'admin'");
        DB::statement("UPDATE users SET role = 'user' WHERE role = 'petani' OR role = 'penggilingan'");
        
        // Set enum back to old values only
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'user') NOT NULL DEFAULT 'user'");
    }
};
