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
            $table->string('voucher_type'); // nominal, percentage, free_item
            $table->decimal('voucher_value', 15, 2);
            $table->decimal('voucher_max_discount', 15, 2)->nullable();
            $table->decimal('voucher_min_purchase', 15, 2)->nullable();
            $table->datetime('start_date');
            $table->datetime('end_date')->nullable();
            $table->string('reason')->nullable();
            $table->string('changed_by', 50)->nullable();

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
