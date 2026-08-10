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
        Schema::create('cogs_recipe_items', function (Blueprint $table) {
            $table->id('cogs_recipe_item_id');
            $table->foreignId('cogs_recipe_id')->constrained('cogs_recipes', 'cogs_recipe_id')->cascadeOnDelete();
            $table->foreignId('cogs_raw_material_id')->constrained('cogs_raw_materials', 'cogs_raw_material_id')->cascadeOnDelete();
            $table->decimal('ingredient_qty', 15, 4)->default(0); // takaran gram/ml/pcs
            $table->decimal('ingredient_cost', 15, 2)->default(0); // subtotal modal bahan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cogs_recipe_items');
    }
};
