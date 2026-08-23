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
        Schema::create('daily_closings', function (Blueprint $table) {
            $table->id();
            $table->string('company_id')->nullable();
            $table->unsignedBigInteger('cashier_id')->nullable();
            
            $table->integer('shift_number')->default(1);
            $table->string('shift_name', 50)->default('Shift 1');
            $table->date('business_date');
            
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            
            $table->decimal('starting_cash', 15, 2)->default(0);
            $table->decimal('system_cash_sales', 15, 2)->default(0);
            $table->decimal('system_non_cash_sales', 15, 2)->default(0);
            $table->decimal('cash_in_amount', 15, 2)->default(0);
            $table->decimal('cash_out_amount', 15, 2)->default(0);
            $table->decimal('system_expected_cash', 15, 2)->default(0);
            
            $table->decimal('actual_cash_counted', 15, 2)->default(0);
            $table->decimal('cash_difference', 15, 2)->default(0);
            
            $table->text('notes')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_closings');
    }
};
