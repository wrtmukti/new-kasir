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
        Schema::create('companies', function (Blueprint $table) {
            $table->ulid('company_id')->primary();
            $table->string('company_name')->nullable();
            $table->string('company_code')->unique()->nullable();
            $table->string('company_slug')->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_phone')->nullable();
            $table->text('company_address')->nullable();
            $table->string('category_image')->nullable();
            $table->tinyInteger('company_status')->default(0);

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
