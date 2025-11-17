<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert default super admin
        DB::table('users')->insert([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'username' => 'superadmin',
            'email' => 'superadmin@system.com',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert diamond bundles
        $bundles = [
            ['name' => 'Starter Pack', 'diamond_amount' => 50, 'price' => 50.00, 'badge_type' => 'primary', 'badge_text' => 'Starter', 'is_active' => true, 'display_order' => 1],
            ['name' => 'Basic Pack', 'diamond_amount' => 100, 'price' => 100.00, 'badge_type' => null, 'badge_text' => null, 'is_active' => true, 'display_order' => 2],
            ['name' => 'Popular Choice', 'diamond_amount' => 250, 'price' => 250.00, 'badge_type' => 'primary', 'badge_text' => 'Popular', 'is_active' => true, 'display_order' => 3],
            ['name' => 'Best Value', 'diamond_amount' => 500, 'price' => 500.00, 'badge_type' => 'warning', 'badge_text' => 'Best Value', 'is_active' => true, 'display_order' => 4],
            ['name' => 'Advanced Pack', 'diamond_amount' => 750, 'price' => 750.00, 'badge_type' => null, 'badge_text' => null, 'is_active' => true, 'display_order' => 5],
            ['name' => 'Premium Pack', 'diamond_amount' => 1000, 'price' => 1000.00, 'badge_type' => 'danger', 'badge_text' => 'Premium', 'is_active' => true, 'display_order' => 6],
        ];

        foreach ($bundles as $bundle) {
            DB::table('diamond_bundles')->insert($bundle);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')->where('email', 'superadmin@system.com')->delete();
        DB::table('diamond_bundles')->truncate();
    }
};