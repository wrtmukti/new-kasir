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
        Schema::create('setting_outlets', function (Blueprint $table) {
            $table->id();
            $table->string('outlet_id')->nullable();
            $table->string('outlet_name')->nullable();
            $table->string('payment_timing')->default('post_payment'); // post_payment (Bayar di Akhir), pre_payment (Bayar di Awal)
            $table->string('theme')->default('standard'); // tema guest template aktif
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->tinyInteger('delete_status')->default(0); // 0 for not deleted, 1 for deleted
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_outlets');
    }
};
