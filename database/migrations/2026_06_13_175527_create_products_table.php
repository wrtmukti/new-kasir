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
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');

            $table->string('code')->unique();
            $table->string('name');

            $table->decimal('price', 15, 2);

            $table->tinyInteger('type')->default(0);
            $table->boolean('status')->default(true);

            $table->string('image')->nullable();

            $table->foreignId('category_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
