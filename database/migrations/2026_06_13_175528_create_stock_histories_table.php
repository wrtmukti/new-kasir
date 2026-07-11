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
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id('stock_history_id');
            $table->foreignId('stock_id')->constrained('stocks', 'stock_id');
            $table->string('company_id')->nullable();

            $table->string('stock_code')->nullable();
            $table->string('stock_name')->nullable();
            $table->string('stock_slug')->nullable();
            $table->text('stock_description')->nullable();

            $table->string('stock_type')->nullable();
            $table->string('stock_unit', 20)->nullable();
            $table->tinyInteger('stock_counted')->default(1);
            $table->integer('stock_amount')->default(0);
            $table->decimal('stock_price', 15, 2)->nullable();
            $table->tinyInteger('stock_status')->default(1);
            $table->string('stock_image')->nullable();

            $table->string('stock_history_remark')->nullable();
            $table->date('effective_date')->nullable();
            $table->string('action_type')->nullable(); // create / update / delete
            $table->string('changed_by', 50)->nullable();
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
        Schema::dropIfExists('stock_histories');
    }
};
