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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->string('company_id')->nullable();

            $table->string('order_type')->nullable(); // dine in, take away, delivery (opsional)
            $table->string('order_status')->nullable(); // pending, in progress, completed, cancelled
            $table->decimal('order_grand_total', 10, 2)->nullable(); // total order
            $table->text('order_remark')->nullable();

            $table->integer('order_transaction_id')->nullable();
            $table->string('order_table_id')->nullable();
            $table->string('order_customer_id')->nullable();
            // $table->integer('order_voucher_id')->nullable();// nanti kalo mau pake voucher bisa ditambahin pake pivot table


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
        Schema::dropIfExists('orders');
    }
};
