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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['super_admin', 'admin', 'customer'])->default('customer');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            
            // Customer-specific fields
            $table->string('home_address')->nullable();
            $table->string('work_address')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('valid_id_path')->nullable();
            $table->string('facial_recognition_path')->nullable();
            
            // Verification fields
            $table->timestamp('username_updated_at')->nullable();
            $table->timestamp('name_updated_at')->nullable();
            $table->timestamp('contact_number_verified_at')->nullable();
            
            // OTP fields
            $table->string('otp_code')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            
            // Address fields (for single address compatibility)
            $table->string('address_region')->nullable();
            $table->string('address_city')->nullable();
            $table->string('address_district')->nullable();
            $table->string('address_street')->nullable();
            $table->string('address_unit')->nullable();
            $table->enum('address_category', ['home', 'work'])->default('home');
            $table->boolean('is_default_shipping')->default(false);
            
            // Email verification
            $table->string('email_verification_token')->nullable();
            $table->timestamp('email_verification_sent_at')->nullable();
            
            // Social login
            $table->string('google_id')->nullable();
            
            // Virtual currency
            $table->integer('diamond_balance')->default(0);
            $table->integer('pending_diamond_balance')->default(0);
            
            // Multiple addresses (JSON storage)
            $table->json('addresses')->nullable();
            
            // Terms acceptance
            $table->timestamp('terms_accepted_at')->nullable();
            
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};