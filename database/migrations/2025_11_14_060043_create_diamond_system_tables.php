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
        // Diamond bundles table
        Schema::create('diamond_bundles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->integer('diamond_amount');
            $table->decimal('price', 10, 2);
            $table->string('badge_type', 50)->nullable();
            $table->string('badge_text', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('display_order');
            $table->timestamps();
        });

        // Diamond purchases table
        Schema::create('diamond_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->string('customer_email')->nullable();
            $table->string('customer_name')->nullable();
            $table->foreignId('bundle_id')->constrained('diamond_bundles')->onDelete('cascade');
            $table->integer('diamond_amount');
            $table->decimal('amount_paid', 10, 2);
            $table->enum('payment_status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('payment_method');
            $table->string('transaction_id')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('customer_id');
            $table->index('bundle_id');
            $table->index('payment_status');
        });

        // Diamond transactions table (for tracking all diamond movements)
        Schema::create('diamond_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->string('customer_email')->nullable();
            $table->string('customer_name')->nullable();
            $table->enum('transaction_type', ['purchase', 'bid', 'refund', 'bonus']);
            $table->integer('amount');
            $table->text('description')->nullable();
            $table->string('reference_id')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('customer_id');
            $table->index('transaction_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diamond_transactions');
        Schema::dropIfExists('diamond_purchases');
        Schema::dropIfExists('diamond_bundles');
    }
};