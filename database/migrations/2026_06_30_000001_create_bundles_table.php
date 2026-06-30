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
        Schema::create('bundles', function (Blueprint $table) {
            $table->id('bundle_id');
            $table->string('company_id')->nullable();

            $table->string('bundle_code')->nullable();
            $table->string('bundle_name')->nullable();
            $table->string('bundle_slug')->nullable();
            $table->text('bundle_description')->nullable();
            $table->decimal('bundle_price', 15, 2)->nullable();

            $table->tinyInteger('bundle_status')->default(1); // 0 for inactive, 1 for active
            $table->string('bundle_image')->nullable();

            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->tinyInteger('delete_status')->default(0); // 0 for not deleted, 1 for deleted
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bundles');
    }
};
