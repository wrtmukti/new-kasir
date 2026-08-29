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
        Schema::create('cash_drawer_logs', function (Blueprint $table) {
            $table->id();
            $table->string('outlet_id')->nullable();
            $table->unsignedBigInteger('daily_closing_id')->nullable()->index();
            $table->unsignedBigInteger('cashier_id')->nullable();
            $table->enum('type', ['in', 'out']); // 'in' = Kas Masuk (Paid-In), 'out' = Kas Keluar (Paid-Out)
            $table->string('category', 50)->default('general'); // 'owner_topup', 'petty_cash', 'cash_drop', 'cash_correction', 'other'
            $table->decimal('amount', 15, 2);
            $table->string('reason', 255);
            $table->string('created_by', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_drawer_logs');
    }
};
