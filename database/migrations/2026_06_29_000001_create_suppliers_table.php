<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id('supplier_id');
            $table->string('company_id')->nullable();

            $table->string('supplier_code')->nullable();     // SUP-001
            $table->string('supplier_name');                  // PT Sumber Rejeki
            $table->string('supplier_contact')->nullable();   // Contact person
            $table->string('supplier_phone')->nullable();
            $table->text('supplier_address')->nullable();
            $table->tinyInteger('supplier_status')->default(1); // 0 inactive, 1 active

            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->tinyInteger('delete_status')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
