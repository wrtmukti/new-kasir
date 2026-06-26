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
        Schema::create('product_logs', function (Blueprint $table) {
            $table->id('product_log_id');
            $table->foreignId('product_id')->constrained('products', 'product_id');
            $table->string('company_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();

            $table->string('product_code')->nullable();
            $table->string('product_name')->nullable();
            $table->string('product_slug')->nullable();
            $table->text('product_description')->nullable();
            $table->decimal('product_price', 15, 2)->nullable();
            $table->decimal('product_discount', 15, 2)->nullable();
            $table->decimal('product_grand_total', 15, 2)->nullable();


            $table->tinyInteger('product_status')->default(1); //0 for inactive, 1 for active
            $table->string('product_image')->nullable();

            $table->string('category_remark')->nullable();
            $table->date('effective_date')->nullable(); // tgl data ini mulai berlaku
            $table->string('action_type')->nullable(); // create / update / delete
            $table->string('changed_by', 50)->nullable();
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->tinyInteger('delete_status')->default(0); //0 for not deleted, 1 for deleted
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_logs');
    }
};
