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
        Schema::create('system_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username', 60)->unique();
            $table->string('email', 150)->unique();
            $table->string('password');
            $table->enum('role', ['super_admin', 'system_admin', 'support'])->default('system_admin');
            $table->string('phone', 30)->nullable();
            $table->string('avatar')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->rememberToken();
            $table->string('created_by', 60)->nullable();
            $table->string('updated_by', 60)->nullable();
            $table->tinyInteger('delete_status')->default(0);
            $table->timestamps();

            $table->index(['username', 'email']);
            $table->index(['role', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_users');
    }
};
