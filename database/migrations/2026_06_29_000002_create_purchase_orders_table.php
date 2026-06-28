<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id('po_id');
            $table->string('company_id')->nullable();

            $table->string('po_code');                         // PO-20260629-001
            $table->datetime('po_date');
            $table->string('supplier_id')->nullable();          // FK integer ke suppliers (nullable, no constraint)
            $table->string('po_status');                        // draft | ordered | partial | completed | cancelled
            $table->decimal('po_total_amount', 15, 2)->default(0);
            $table->text('po_notes')->nullable();

            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->tinyInteger('delete_status')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
