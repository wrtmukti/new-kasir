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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key', 80)->unique();
            $table->text('setting_value')->nullable();
            $table->string('setting_group', 50)->default('general'); // general, security, backup, mail, maintenance
            $table->string('display_name', 120)->nullable();
            $table->text('description')->nullable();
            $table->string('created_by', 60)->nullable();
            $table->string('updated_by', 60)->nullable();
            $table->timestamps();

            $table->index(['setting_key', 'setting_group']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
