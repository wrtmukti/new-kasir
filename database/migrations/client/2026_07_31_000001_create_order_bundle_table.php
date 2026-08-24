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
        Schema::create('order_bundle', function (Blueprint $table) {
            $table->id();
            $table->string('outlet_id')->nullable();

            $table->string('order_id')->nullable();       // FK ke orders
            $table->string('transaction_id')->nullable(); // FK ke transactions — terisi pas complete

            // SNAPSHOT — data bundle pas dipesan (frozen, gak berubah kalo master diupdate)
            $table->string('bundle_id')->nullable();      // FK ke bundles
            $table->string('bundle_name');
            $table->decimal('bundle_price', 15, 2)->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('subtotal', 15, 2)->nullable(); // bundle_price * quantity

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
        Schema::dropIfExists('order_bundle');
    }
};
