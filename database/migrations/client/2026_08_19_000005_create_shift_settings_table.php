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
        Schema::create('shift_settings', function (Blueprint $table) {
            $table->id();
            $table->string('outlet_id')->nullable();
            $table->time('daily_cutoff_time')->default('03:00:00');
            $table->string('shift_mode', 30)->default('auto_master'); // auto_master, manual, single_daily
            $table->tinyInteger('auto_lock_unclosed')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_settings');
    }
};
