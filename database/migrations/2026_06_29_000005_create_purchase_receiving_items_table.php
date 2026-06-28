<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_receiving_items', function (Blueprint $table) {
            $table->id('receiving_item_id');
            $table->string('receiving_id')->nullable();          // FK integer ke purchase_receivings
            $table->string('po_item_id')->nullable();            // FK integer ke purchase_order_items
            $table->string('stock_id')->nullable();              // FK integer ke stocks

            $table->integer('received_qty');                     // jumlah yg diterima skrg
            $table->decimal('received_price', 15, 2)->default(0); // harga pas diterima
            $table->decimal('subtotal', 15, 2)->default(0);       // received_qty * received_price
            $table->text('notes')->nullable();

            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->tinyInteger('delete_status')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_receiving_items');
    }
};
