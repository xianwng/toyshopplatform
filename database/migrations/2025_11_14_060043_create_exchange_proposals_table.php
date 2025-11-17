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
        Schema::create('exchange_proposals', function (Blueprint $table) {
            $table->id();
            
            // Proposal participants
            $table->foreignId('sender_id')->constrained('users')->comment('User proposing the exchange');
            $table->foreignId('receiver_id')->constrained('users')->comment('Trade owner receiving proposal');
            $table->foreignId('receiver_trade_id')->constrained('trades')->comment('Trade they want to exchange for');
            
            // Proposed item details
            $table->string('proposed_item_name')->comment('Name of item being offered');
            $table->string('proposed_item_brand')->nullable();
            $table->string('proposed_item_category');
            $table->string('proposed_item_condition');
            $table->string('proposed_item_location');
            $table->text('proposed_item_description');
            $table->json('proposed_item_images')->nullable()->comment('Array of image paths');
            $table->json('proposed_item_documents')->nullable()->comment('Array of document paths');
            
            // Exchange terms
            $table->decimal('cash_amount', 10, 2)->default(0.00)->comment('Additional cash offered');
            $table->enum('delivery_method', ['cashOnDelivery', 'meetupOnly'])->default('cashOnDelivery');
            $table->string('meetup_location')->nullable()->comment('Specific meetup location if meetupOnly');
            
            // Communication
            $table->text('message')->nullable()->comment('Optional message to receiver');
            
            // Status and tracking
            $table->enum('status', ['pending', 'accepted', 'rejected', 'cancelled'])->default('pending');
            $table->timestamp('responded_at')->nullable()->comment('When receiver responded');
            
            $table->timestamps();
            
            // Indexes
            $table->index('sender_id');
            $table->index('receiver_id');
            $table->index('receiver_trade_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_proposals');
    }
};