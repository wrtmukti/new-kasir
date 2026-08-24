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
        Schema::create('cogs_recipes', function (Blueprint $table) {
            $table->id('cogs_recipe_id');
            $table->string('outlet_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable(); // relasi opsional ke menu products
            $table->string('recipe_name');
            $table->string('recipe_category')->nullable();
            $table->decimal('target_food_cost', 5, 2)->default(30.00); // % target Food Cost
            $table->decimal('estimated_cogs', 15, 2)->default(0.00); // total modal ideal terhitung
            $table->decimal('suggested_price', 15, 2)->default(0.00); // estimated_cogs / (target_food_cost / 100)
            $table->text('notes')->nullable();

            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->tinyInteger('delete_status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cogs_recipes');
    }
};
