<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_receivings', function (Blueprint $table) {
            $table->id('receiving_id');
            $table->string('company_id')->nullable();

            $table->string('receiving_code');                   // RCV-20260629-001
            $table->datetime('receiving_date');
            $table->string('po_id')->nullable();                 // FK integer ke purchase_orders
            $table->string('po_code')->nullable();               // SNAPSHOT kode PO
            $table->string('receiving_status');                  // pending | completed
            $table->text('receiving_notes')->nullable();

            $table->string('received_by', 50)->nullable();
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->tinyInteger('delete_status')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_receivings');
    }
};
