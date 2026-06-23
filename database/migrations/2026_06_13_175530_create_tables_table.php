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
        Schema::create('tables', function (Blueprint $table) {
            $table->ulid('table_id');
            $table->string('company_id')->nullable();
            
            $table->integer('table_number')->nullable();
            $table->string('table_status')->nullable();// inactive, active, reserved, occupied
            $table->integer('table_capacity')->nullable();
            $table->text('table_description')->nullable();
            
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->tinyInteger('delete_status')->default(0);//0 for not deleted, 1 for deleted
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
