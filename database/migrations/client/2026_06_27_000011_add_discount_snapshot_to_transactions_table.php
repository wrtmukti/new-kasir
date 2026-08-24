<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('discount_id')->nullable()->after('transaction_grand_total');
            $table->string('discount_name')->nullable()->after('discount_id');
            $table->string('discount_type')->nullable()->after('discount_name');
            $table->decimal('discount_value', 15, 2)->nullable()->after('discount_type');
            $table->decimal('discount_amount', 15, 2)->nullable()->after('discount_value');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('discount_id');
            $table->dropColumn('discount_name');
            $table->dropColumn('discount_type');
            $table->dropColumn('discount_value');
            $table->dropColumn('discount_amount');
        });
    }
};
