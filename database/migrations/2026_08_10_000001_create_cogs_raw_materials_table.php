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
        Schema::create('cogs_raw_materials', function (Blueprint $table) {
            $table->id('cogs_raw_material_id');
            $table->string('company_id')->nullable();
            $table->string('raw_material_code')->nullable();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('unit', 20)->default('kg'); // kg, liter, gr, ml, pcs, butir
            $table->decimal('amount', 15, 4)->default(0); // stok fisik mentah
            $table->decimal('min_amount', 15, 4)->default(0); // batas minimal alert
            $table->decimal('price_per_unit', 15, 2)->default(0); // harga beli per unit
            $table->decimal('loss_percent', 5, 2)->default(0); // % susut
            $table->decimal('yield_percent', 5, 2)->default(100); // 100 - loss
            $table->decimal('effective_price', 15, 4)->default(0); // harga efektif setelah susut
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
        Schema::dropIfExists('cogs_raw_materials');
    }
};
