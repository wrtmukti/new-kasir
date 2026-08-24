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
        Schema::create('order_voucher', function (Blueprint $table) {
            $table->id();
            $table->string('outlet_id')->nullable();

            $table->string('order_id')->nullable(); // FK ke orders

            // SNAPSHOT — data voucher pas dipake
            $table->string('voucher_code');
            $table->string('voucher_type');             // nominal, percentage, free_item
            $table->decimal('voucher_value', 15, 2);
            $table->decimal('voucher_max_discount', 15, 2)->nullable();
            $table->decimal('voucher_amount', 15, 2);   // hasil potongan

            $table->string('created_by', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_voucher');
    }
};
