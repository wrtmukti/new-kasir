<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->string('company_id')->nullable();
            $table->string('stock_id')->nullable();              // FK integer ke stocks

            $table->string('reference_type');                    // purchase_receiving, adjustment, sale
            $table->string('reference_code');                    // RCV-001, ADJ-001, TRX-001
            $table->string('type');                              // in (masuk), out (keluar), adjustment
            $table->integer('qty');                              // +/- quantity
            $table->decimal('price', 15, 2)->nullable();         // harga per unit
            $table->decimal('total', 15, 2)->nullable();          // qty * price
            $table->integer('stock_before')->nullable();          // stok sebelum
            $table->integer('stock_after')->nullable();           // stok sesudah
            $table->text('notes')->nullable();

            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->tinyInteger('delete_status')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_logs');
    }
};
