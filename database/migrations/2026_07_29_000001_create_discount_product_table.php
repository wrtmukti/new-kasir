<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount_product', function (Blueprint $table) {
            $table->id();
            $table->string('company_id')->nullable();

            $table->string('product_id')->nullable();   // FK ke products.product_id
            $table->string('discount_id')->nullable();   // FK ke discounts.discount_id

            // Timeline — ini yg bikin pivot jadi history assignment
            $table->datetime('start_date')->nullable();   // kapan attach
            $table->datetime('end_date')->nullable();     // null = masih aktif, terisi = diganti/dilepas

            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->tinyInteger('delete_status')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_product');
    }
};
