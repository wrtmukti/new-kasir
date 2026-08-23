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
        Schema::create('service_charges', function (Blueprint $table) {
            $table->id('service_charge_id');
            $table->string('company_id')->nullable();
            $table->string('service_name', 100);
            $table->decimal('rate_percent', 5, 2)->default(5.00);
            $table->tinyInteger('is_taxable')->default(1);
            $table->tinyInteger('is_active')->default(1);
            $table->tinyInteger('delete_status')->default(0);
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_charges');
    }
};
