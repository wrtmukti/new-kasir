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
        Schema::create('database_connections', function (Blueprint $table) {
            $table->id();
            $table->string('client_id', 50)->unique();
            $table->string('database_name', 100);
            $table->string('server_host', 100)->default('127.0.0.1');
            $table->integer('server_port')->default(3306);
            $table->enum('connection_status', ['connected', 'disconnected', 'warning', 'error'])->default('disconnected');
            $table->decimal('latency_ms', 8, 2)->default(0);
            $table->decimal('database_size_mb', 10, 2)->default(0);
            $table->integer('tables_count')->default(0);
            $table->string('migration_version', 100)->nullable();
            $table->timestamp('last_health_check_at')->nullable();
            $table->timestamp('last_backup_at')->nullable();
            $table->text('status_message')->nullable();
            $table->tinyInteger('delete_status')->default(0);
            $table->timestamps();

            $table->index(['client_id', 'connection_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('database_connections');
    }
};
