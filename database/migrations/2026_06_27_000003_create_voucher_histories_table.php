<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voucher_histories', function (Blueprint $table) {
            $table->id('history_id');
            $table->string('voucher_id')->nullable(); // no FK
            $table->string('company_id')->nullable();

            $table->string('voucher_code');
            $table->string('voucher_name');
            $table->text('voucher_description')->nullable();
            $table->string('voucher_type'); // nominal, percentage, free_item
            $table->decimal('voucher_value', 15, 2);
            $table->decimal('voucher_max_discount', 15, 2)->nullable();
            $table->decimal('voucher_min_purchase', 15, 2)->nullable();
            $table->string('voucher_applicable_to')->nullable();  // all, specific_products, specific_categories
            $table->integer('voucher_usage_limit')->nullable();     // total usage limit
            $table->integer('voucher_usage_per_customer')->nullable(); // per customer limit
            $table->datetime('voucher_start_date');
            $table->datetime('voucher_end_date')->nullable();
            $table->tinyInteger('voucher_status')->default(1);     // 0 inactive, 1 active
            $table->string('action', 50)->nullable(); // create, update, delete
            $table->string('user_id', 50)->nullable();

            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->tinyInteger('delete_status')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_histories');
    }
};
