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
            $table->ulid('company_id')->primary();//1//2
            $table->string('company_name')->nullable();// geprek gambus/
            $table->string('company_code')->nullable();// GGB 
            $table->string('company_branch')->nullable();// jakrta // jogja
            $table->string('company_slug')->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_phone')->nullable();
            $table->text('company_address')->nullable();
            $table->string('company_image')->nullable();
            $table->tinyInteger('company_status')->default(1);//0 for inactive, 1 for active

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->tinyInteger('delete_status')->default(0);//0 for not deleted, 1 for deleted
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
