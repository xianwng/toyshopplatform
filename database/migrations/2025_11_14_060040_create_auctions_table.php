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
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('product_name');
            $table->string('brand')->default('Unknown');
            $table->string('condition')->default('Unknown');
            $table->string('category')->default('General');
            $table->string('rarity')->default('Common');
            $table->text('description')->nullable();
            $table->json('product_img')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->integer('starting_price');
            $table->integer('buyout_bid')->nullable();
            $table->dateTime('end_time');
            $table->timestamp('scheduled_time')->nullable();
            
            // Status and winner
            $table->enum('status', ['pending', 'approved', 'active', 'ended', 'completed', 'rejected'])->default('pending');
            $table->foreignId('winner_id')->nullable()->constrained('users');
            $table->integer('current_bid')->nullable();
            
            // Escrow and payout system
            $table->enum('payout_status', ['pending', 'approved', 'rejected', 'released', 'refunded'])->default('pending');
            $table->decimal('payout_amount', 10, 2)->default(0.00);
            $table->timestamp('escrow_held_at')->nullable();
            $table->timestamp('seller_reply_deadline')->nullable();
            $table->timestamp('chat_created_at')->nullable();
            $table->timestamp('item_received_at')->nullable();
            $table->timestamp('payout_approved_at')->nullable();
            $table->foreignId('payout_approved_by')->nullable()->constrained('users');
            $table->timestamp('escrow_released_at')->nullable();
            
            // Verification fields
            $table->string('owner_proof')->nullable();
            $table->string('market_value_proof')->nullable();
            $table->text('reference_links')->nullable();
            $table->decimal('minimum_market_value', 10, 2)->default(0.00);
            
            // Delivery
            $table->enum('delivery_method', ['seller_delivery', 'pickup', 'courier'])->default('seller_delivery');
            $table->decimal('delivery_cost', 10, 2)->default(0.00);
            $table->boolean('terms_accepted')->default(false);
            
            // Verification
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'end_time']);
            $table->index('user_id');
            $table->index('winner_id');
            $table->index('verified_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auctions');
    }
};