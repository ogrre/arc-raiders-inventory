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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('game_id')->unique(); // ID from game JSON
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->nullable(); // Basic Material, Recyclable, etc.
            $table->string('rarity')->nullable(); // Common, Uncommon, Rare, etc.
            $table->integer('value')->nullable(); // Sell value
            $table->string('image_url')->nullable();
            $table->decimal('weight_kg', 8, 2)->nullable();
            $table->integer('stack_size')->nullable();
            $table->string('found_in')->nullable(); // Where to find the item
            $table->json('effects')->nullable(); // For consumables
            $table->json('recycles_into')->nullable(); // For recyclables
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
