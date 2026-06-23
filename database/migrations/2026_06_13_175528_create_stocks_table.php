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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id('stock_id');
            $table->string('company_id')->nullable();
            
            $table->string('stock_code')->nullable();
            $table->string('stock_name')->nullable();
            $table->string('stock_slug')->nullable();
            $table->text('stock_description')->nullable();

            $table->string('stock_type')->nullable();

            $table->string('stock_unit', 20)->nullable();// gr, kg, pcs, etc
            $table->tinyInteger('stock_counted')->default(1);//0 for not counted, 1 for counted
            $table->integer('stock_amount')->default(0);
            $table->decimal('stock_price', 15, 2)->nullable();// price per unit
            $table->tinyInteger('stock_status')->default(1);//0 for inactive, 1 for active
            $table->string('stock_image')->nullable();

            $table->string('category_remark')->nullable();
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->tinyInteger('delete_status')->default(0);//0 for not deleted, 1 for deleted
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
