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
        Schema::create('hpp_financial_reports', function (Blueprint $table) {
            $table->id('hpp_financial_report_id');
            $table->string('outlet_id')->nullable();
            $table->integer('year');
            $table->integer('month');
            $table->decimal('total_revenue', 15, 2)->default(0.00); // Omzet kasir
            $table->decimal('total_cogs_estimated', 15, 2)->default(0.00); // Total estimasi COGS porsi terjual
            $table->decimal('total_waste_cost', 15, 2)->default(0.00); // Kerugian bahan terbuang/basi
            $table->decimal('total_labor_cost', 15, 2)->default(0.00); // Total Gaji
            $table->decimal('total_overhead_cost', 15, 2)->default(0.00); // Total Operasional
            $table->decimal('gross_profit', 15, 2)->default(0.00); // Revenue - COGS
            $table->decimal('net_profit_estimated', 15, 2)->default(0.00); // Revenue - COGS - Waste - Labor - Overhead
            $table->text('notes')->nullable();

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
        Schema::dropIfExists('hpp_financial_reports');
    }
};
