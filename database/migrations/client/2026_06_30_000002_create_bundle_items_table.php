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
        Schema::create('bundle_items', function (Blueprint $table) {
            $table->id('bundle_item_id');
            $table->foreignId('bundle_id')->constrained('bundles', 'bundle_id');
            $table->foreignId('product_id')->constrained('products', 'product_id');
            $table->integer('quantity')->default(1);
            $table->decimal('price_snapshot', 15, 2)->nullable(); // harga produk saat bundle dibuat

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
        Schema::dropIfExists('bundle_items');
    }
};
