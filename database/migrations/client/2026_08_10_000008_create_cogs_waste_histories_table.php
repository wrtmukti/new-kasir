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
        Schema::create('cogs_waste_histories', function (Blueprint $table) {
            $table->id('cogs_waste_history_id');
            $table->foreignId('cogs_waste_log_id')->constrained('cogs_waste_logs', 'cogs_waste_log_id')->cascadeOnDelete();
            $table->string('outlet_id')->nullable();
            $table->foreignId('cogs_raw_material_id')->constrained('cogs_raw_materials', 'cogs_raw_material_id')->cascadeOnDelete();
            $table->decimal('qty_lost', 15, 4);
            $table->decimal('waste_cost', 15, 2);
            $table->string('reason')->default('Rotten/Basi');
            $table->date('loss_date');

            $table->string('action_type')->nullable(); // create / update / delete
            $table->string('changed_by', 50)->nullable();
            $table->text('history_remark')->nullable();

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
        Schema::dropIfExists('cogs_waste_histories');
    }
};
