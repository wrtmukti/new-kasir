<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id('item_id');
            $table->string('company_id')->nullable();
            $table->string('transaction_id')->nullable(); // no FK
            $table->string('product_id')->nullable(); // no FK

            // SNAPSHOT — data produk & diskon pas transaksi
            $table->string('product_name');
            $table->decimal('price', 15, 2);
            $table->string('discount_type')->nullable(); // percentage, nominal
            $table->decimal('discount_value', 15, 2)->nullable();
            $table->decimal('discount_amount', 15, 2)->nullable(); // hasil potongan
            $table->integer('qty');
            $table->decimal('subtotal', 15, 2); // (price - discount_amount) * qty

            $table->text('note')->nullable();
            $table->string('created_by', 50)->nullable();

            $table->tinyInteger('delete_status')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
