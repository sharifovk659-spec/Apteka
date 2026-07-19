<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_active', 'stock']);
            $table->index(['is_active', 'is_daily_product']);
            $table->index(['is_active', 'is_bestseller']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('customer_phone');
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'stock']);
            $table->dropIndex(['is_active', 'is_daily_product']);
            $table->dropIndex(['is_active', 'is_bestseller']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['customer_phone']);
            $table->dropIndex(['status', 'created_at']);
        });
    }
};
