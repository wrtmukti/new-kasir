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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('subscription_id', 60)->unique(); // SUB-CLI001-202609
            $table->string('client_id', 50);
            $table->unsignedBigInteger('plan_id');
            $table->date('start_date');
            $table->date('expired_date');
            $table->enum('status', [
                'trial',
                'active',
                'expiring_soon',
                'expired',
                'suspended',
                'cancelled'
            ])->default('trial');

            $table->string('billing_reference', 100)->nullable();
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->string('payment_method', 50)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->tinyInteger('auto_renew')->default(0);
            $table->text('notes')->nullable();

            $table->string('created_by', 60)->nullable();
            $table->string('updated_by', 60)->nullable();
            $table->tinyInteger('delete_status')->default(0);
            $table->timestamps();

            $table->index(['client_id', 'status']);
            $table->index(['expired_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
