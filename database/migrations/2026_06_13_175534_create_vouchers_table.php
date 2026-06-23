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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id('voucher_id');
            $table->string('company_id')->nullable();

            $table->string('voucher_code')->nullable();           // DISC10, BDAY15
            $table->string('voucher_name')->nullable();            // Diskon 10%
            $table->text('voucher_description')->nullable();
            $table->string('voucher_type')->nullable();            // nominal, percentage, free_item
            $table->decimal('voucher_value', 15, 2)->nullable();  // nominal 10000 atau percentage 10
            $table->decimal('voucher_max_discount', 15, 2)->nullable(); // cap diskon
            $table->decimal('voucher_min_purchase', 15, 2)->nullable(); // min belanja
            $table->string('voucher_applicable_to')->nullable();  // all, specific_products, specific_categories
            $table->integer('voucher_usage_limit')->nullable();     // total usage limit
            $table->integer('voucher_usage_per_customer')->nullable(); // per customer limit
            $table->datetime('voucher_start_date')->nullable();
            $table->datetime('voucher_end_date')->nullable();
            $table->tinyInteger('voucher_status')->default(1);     // 0 inactive, 1 active

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
        Schema::dropIfExists('payments');
    }
};
