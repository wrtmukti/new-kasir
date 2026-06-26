<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_voucher', function (Blueprint $table) {
            $table->id();
            $table->string('company_id')->nullable();
            $table->string('transaction_id')->nullable(); // no FK
            $table->string('voucher_id')->nullable(); // no FK

            // SNAPSHOT — data voucher pas transaksi
            $table->string('voucher_code');
            $table->string('voucher_type'); // nominal, percentage, free_item
            $table->decimal('voucher_value', 15, 2);
            $table->decimal('voucher_max_discount', 15, 2)->nullable();
            $table->decimal('voucher_amount', 15, 2); // hasil potongan

            $table->string('created_by', 50)->nullable();

            $table->tinyInteger('delete_status')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_voucher');
    }
};
