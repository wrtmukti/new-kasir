<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id('discount_id');
            $table->string('company_id')->nullable();

            $table->string('discount_name');
            $table->string('discount_type'); // percentage, nominal
            $table->decimal('discount_value', 15, 2);
            $table->decimal('discount_max_amount', 15, 2)->nullable(); // cap maksimal
            $table->text('discount_description')->nullable();
            $table->tinyInteger('discount_status')->default(1); // 0 inactive, 1 active

            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->tinyInteger('delete_status')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
