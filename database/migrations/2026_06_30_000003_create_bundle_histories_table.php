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
        Schema::create('bundle_histories', function (Blueprint $table) {
            $table->id('bundle_history_id');
            $table->string('bundle_id')->nullable(); // no FK constraint
            $table->string('company_id')->nullable();

            // Snapshot of bundle data
            $table->string('bundle_code')->nullable();
            $table->string('bundle_name')->nullable();
            $table->string('bundle_slug')->nullable();
            $table->text('bundle_description')->nullable();
            $table->decimal('bundle_price', 15, 2)->nullable();
            $table->tinyInteger('bundle_status')->default(1);
            $table->string('bundle_image')->nullable();

            $table->string('bundle_history_remark')->nullable();
            $table->date('effective_date')->nullable();
            $table->string('action_type')->nullable(); // create / update / delete
            $table->string('changed_by', 50)->nullable();

            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->tinyInteger('delete_status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bundle_histories');
    }
};
