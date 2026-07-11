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
        Schema::create('customers', function (Blueprint $table) {
            $table->id('customer_id'); // kusus customer
            $table->string('company_id')->nullable();

            $table->integer('transaction_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable(); // untuk info promo
            $table->string('customer_phone')->nullable(); // untuk info promo
            $table->text('customer_address')->nullable(); //opsinal (misal untuk pengiriman)
            $table->string('customer_notes')->nullable();

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
        Schema::dropIfExists('customers');
    }
};
