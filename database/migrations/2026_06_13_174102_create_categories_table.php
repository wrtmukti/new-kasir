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
        Schema::create('categories', function (Blueprint $table) {
            $table->id('category_id');
            $table->string('company_id')->nullable();

            $table->string('category_name', 100)->nullable();
            $table->string('category_slug')->nullable();
            $table->text('category_description')->nullable();
            $table->string('category_type', 100)->nullable();
            $table->tinyInteger('category_status')->default('0');
            $table->string('category_image')->nullable();
            $table->string('category_remark')->nullable();

            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
