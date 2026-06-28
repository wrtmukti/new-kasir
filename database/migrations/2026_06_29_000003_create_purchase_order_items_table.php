<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id('po_item_id');
            $table->string('po_id')->nullable();                // FK integer ke purchase_orders
            $table->string('stock_id')->nullable();              // FK integer ke stocks

            $table->integer('qty');                              // jumlah dipesan
            $table->decimal('price', 15, 2)->default(0);         // harga beli per unit
            $table->decimal('subtotal', 15, 2)->default(0);      // qty * price
            $table->integer('received_qty')->default(0);          // jumlah yg udah diterima
            $table->text('notes')->nullable();

            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->tinyInteger('delete_status')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
