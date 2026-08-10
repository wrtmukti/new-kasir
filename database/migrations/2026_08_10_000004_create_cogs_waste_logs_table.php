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
        Schema::create('cogs_waste_logs', function (Blueprint $table) {
            $table->id('cogs_waste_log_id');
            $table->string('company_id')->nullable();
            $table->foreignId('cogs_raw_material_id')->constrained('cogs_raw_materials', 'cogs_raw_material_id')->cascadeOnDelete();
            $table->decimal('qty_lost', 15, 4); // jumlah terbuang
            $table->decimal('waste_cost', 15, 2); // rupiah kerugian
            $table->string('reason')->default('Rotten/Basi'); // Basi, Rusak, Tumpah, Expired
            $table->date('loss_date');
            $table->text('notes')->nullable();

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
        Schema::dropIfExists('cogs_waste_logs');
    }
};
