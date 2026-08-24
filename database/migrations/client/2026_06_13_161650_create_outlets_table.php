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
        Schema::create('outlets', function (Blueprint $table) {
            $table->ulid('outlet_id')->primary();
            $table->string('outlet_name')->nullable();
            $table->string('outlet_code')->nullable();
            $table->string('outlet_branch')->nullable();
            $table->string('outlet_slug')->nullable();
            $table->string('outlet_email')->nullable();
            $table->string('outlet_phone')->nullable();
            $table->text('outlet_address')->nullable();
            $table->string('outlet_image')->nullable();
            $table->tinyInteger('outlet_status')->default(1); // 0 for inactive, 1 for active

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();

            $table->tinyInteger('delete_status')->default(0); // 0 for not deleted, 1 for deleted
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outlets');
    }
};
