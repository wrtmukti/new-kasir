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
        Schema::create('cogs_recipe_histories', function (Blueprint $table) {
            $table->id('cogs_recipe_history_id');
            $table->foreignId('cogs_recipe_id')->constrained('cogs_recipes', 'cogs_recipe_id')->cascadeOnDelete();
            $table->string('company_id')->nullable();
            $table->string('recipe_name');
            $table->decimal('target_food_cost', 5, 2)->default(30.00);
            $table->decimal('estimated_cogs', 15, 2)->default(0.00);
            $table->decimal('suggested_price', 15, 2)->default(0.00);
            $table->json('snapshot_items_json')->nullable(); // snapshot takaran & bahan saat diubah

            $table->string('action_type')->nullable(); // create / update / delete
            $table->string('changed_by', 50)->nullable();
            $table->date('effective_date')->nullable();
            $table->text('history_remark')->nullable();

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
        Schema::dropIfExists('cogs_recipe_histories');
    }
};
