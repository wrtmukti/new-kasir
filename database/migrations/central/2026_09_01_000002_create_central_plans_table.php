<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations on Central Database.
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_code', 50)->unique();
            $table->string('plan_name', 100);
            $table->string('badge_label', 50)->nullable();
            $table->text('description')->nullable();
            $table->integer('max_outlets')->default(1); // batas jumlah cabang
            $table->integer('max_users')->default(5); // batas jumlah kasir & staff
            $table->integer('max_storage_mb')->default(500); // batas upload gambar menu
            $table->json('features_json')->nullable(); // flag modul: ['cogs' => true, 'waste' => true, 'qr_guest' => true, 'custom_theme' => true]
            $table->decimal('price_monthly', 12, 2)->default(0);
            $table->decimal('price_yearly', 12, 2)->default(0);
            $table->integer('trial_days')->default(14);
            $table->tinyInteger('is_active')->default(1);
            $table->integer('sort_order')->default(0);
            $table->string('created_by', 60)->nullable();
            $table->string('updated_by', 60)->nullable();
            $table->tinyInteger('delete_status')->default(0);
            $table->timestamps();

            $table->index(['plan_code', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
