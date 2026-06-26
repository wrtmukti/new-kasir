<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('product_discount_type')->nullable()->after('product_discount');
            $table->decimal('product_discount_value', 15, 2)->nullable()->after('product_discount_type');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('product_discount_type');
            $table->dropColumn('product_discount_value');
        });
    }
};
