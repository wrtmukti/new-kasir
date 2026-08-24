<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('taxes', function (Blueprint $table) {
            $table->id('tax_id');
            $table->string('outlet_id')->nullable();
            $table->string('tax_name', 100);
            $table->decimal('rate_percent', 5, 2)->default(10.00);
            $table->enum('type', ['inclusive', 'exclusive'])->default('exclusive');
            $table->tinyInteger('is_active')->default(1);
            $table->tinyInteger('delete_status')->default(0);
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};
