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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('brand')->default('Unknown');
            $table->string('category');
            $table->string('condition')->default('Unknown');
            $table->string('rarity')->default('Common');
            $table->text('description')->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->decimal('price', 10, 2)->default(0.00);
            $table->string('model_file')->nullable();
            $table->json('product_images')->nullable();
            
            // Amazon integration fields
            $table->string('asin')->nullable();
            $table->decimal('amazon_price', 10, 2)->nullable();
            $table->integer('amazon_stock')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->enum('sync_status', ['pending', 'synced', 'failed', 'not_found'])->default('pending');
            $table->timestamp('last_sync_attempt')->nullable();
            $table->boolean('use_amazon_data')->default(true);
            
            // Approval system
            $table->enum('status', ['pending', 'approved', 'rejected', 'active'])->default('pending');
            $table->json('shipping_methods')->nullable();
            $table->string('certificate_path')->nullable();
            $table->string('market_value_proof')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('asin');
            $table->index('user_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};