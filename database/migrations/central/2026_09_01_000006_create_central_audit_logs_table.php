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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('actor_type', 50)->default('system_user'); // system_user, client_user, system_cron
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('actor_name', 100)->nullable();
            $table->string('actor_role', 50)->nullable(); // super_admin, system_admin, support, owner
            $table->string('client_id', 50)->nullable();
            $table->string('outlet_id', 50)->nullable(); // outlet context
            $table->string('action', 100); // create_client, suspend_client, restore_backup, login_as_client
            $table->string('target_type', 100)->nullable(); // Client, Database, Subscription, User
            $table->string('target_id', 100)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->enum('result', ['success', 'warning', 'failure'])->default('success');
            $table->json('metadata_json')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['actor_id', 'created_at']);
            $table->index(['client_id', 'action']);
            $table->index(['action', 'result']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
