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
        Schema::create('raw_stock_material_histories', function (Blueprint $table) {
            $table->id('raw_stock_material_history_id');
            $table->foreignId('raw_stock_material_id')->constrained('raw_stock_materials', 'raw_stock_material_id')->cascadeOnDelete();
            $table->string('outlet_id')->nullable();
            $table->string('name');
            $table->string('unit', 20)->default('kg');
            $table->decimal('amount', 15, 4)->default(0);
            $table->decimal('price_per_unit', 15, 2)->default(0);
            $table->decimal('loss_percent', 5, 2)->default(0);
            $table->decimal('yield_percent', 5, 2)->default(100);
            $table->decimal('effective_price', 15, 4)->default(0);

            $table->string('action_type')->nullable(); // create / update / delete / po_receiving / waste / adjustment
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
        Schema::dropIfExists('raw_stock_material_histories');
    }
};
