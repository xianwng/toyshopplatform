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
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('brand')->default('Unknown');
            $table->string('category');
            $table->string('condition');
            $table->text('description');
            $table->string('location')->nullable();
            $table->text('trade_preferences')->nullable();
            $table->enum('status', ['pending', 'approved', 'active', 'inactive', 'rejected', 'completed'])->default('pending');
            $table->json('image')->nullable();
            $table->text('documents')->nullable();
            $table->string('model_file')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};