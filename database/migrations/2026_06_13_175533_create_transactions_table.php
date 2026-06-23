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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id('transaction_id'); // kusus customer
            $table->string('company_id')->nullable();

            $table->string('transaction_code')->nullable();
            $table->string('transaction_date')->nullable(); // pending, in progress, completed, cancelled
            $table->decimal('transaction_tax', 10, 2)->nullable();
            $table->decimal('transaction_subtotal', 10, 2)->nullable();
            $table->decimal('transaction_service_charge', 10, 2)->nullable();
            $table->integer('discount_id')->nullable();
            $table->decimal('transaction_grand_total', 10, 2)->nullable(); //subtotal + tax + service_charge - discount
            $table->string('transaction_status')->nullable(); // pending, in progress, completed, cancelled
            $table->text('transaction_remark')->nullable();
            $table->text('cancel_reason')->nullable();

            $table->integer('payment_id')->nullable();
            $table->string('transaction_table_id')->nullable();
            $table->string('transaction_customer_id')->nullable();
            // $table->integer('transaction_voucher_id')->nullable();// nanti kalo mau pake voucher bisa ditambahin pake pivot table


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
        Schema::dropIfExists('transactions');
    }
};
