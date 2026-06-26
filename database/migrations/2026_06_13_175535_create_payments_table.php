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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id'); // kusus customer
            $table->string('company_id')->nullable();

            $table->integer('transaction_id')->nullable();
            $table->string('payment_metode')->nullable();
            $table->decimal('payment_amount', 10, 2)->nullable();
            $table->string('payment_reference')->nullable(); // pending, in progress, completed, cancelled
            $table->string('payment_status')->nullable(); // pending, in progress, completed, cancelled
            $table->decimal('payment_grand_total', 10, 2)->nullable(); //subtotal + tax + service_charge - discount


            $table->text('payment_remark')->nullable();
            $table->string('payment_date')->nullable(); // pending, in progress, completed, cancelled

            $table->integer('payment_id')->nullable();
            $table->string('payment_table_id')->nullable();
            $table->string('payment_customer_id')->nullable();
            // $table->integer('payment_voucher_id')->nullable();// nanti kalo mau pake voucher bisa ditambahin pake pivot table


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
        Schema::dropIfExists('payments');
    }
};
