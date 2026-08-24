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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('client_id', 50)->unique(); // CLI001, CLI002
            $table->string('client_slug', 80)->unique(); // bagaskara-food
            $table->string('client_name', 150); // PT Bagaskara Food Group
            $table->string('client_code', 50)->nullable(); // e.g. KOPISENJA, BINTANGLIMA
            $table->string('business_name', 150)->nullable(); // Bagaskara Resto & Cafe
            $table->string('owner_name', 100);
            $table->string('owner_email', 150);
            $table->string('owner_phone', 30)->nullable();
            $table->string('address')->nullable();
            $table->string('logo')->nullable();

            // Spesifikasi Database Tenant Terpisah
            $table->string('database_name', 100)->unique(); // new_kasir_cli001
            $table->string('db_host', 100)->default('127.0.0.1');
            $table->integer('db_port')->default(3306);
            $table->string('db_username', 100)->default('root');
            $table->string('db_password', 255)->nullable();

            // Status Lifecycle Klien (Draft, Provisioning, Active, Suspended, Cancelled, Failed)
            $table->enum('status', [
                'draft',
                'provisioning',
                'active',
                'suspended',
                'cancelled',
                'failed_provisioning'
            ])->default('draft');

            $table->text('suspension_reason')->nullable();
            $table->timestamp('provisioned_at')->nullable();
            $table->timestamp('last_active_at')->nullable();

            $table->string('created_by', 60)->nullable();
            $table->string('updated_by', 60)->nullable();
            $table->tinyInteger('delete_status')->default(0);
            $table->timestamps();

            $table->index(['client_id', 'status']);
            $table->index(['owner_email', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
