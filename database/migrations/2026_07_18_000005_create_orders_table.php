<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->text('address');
            $table->string('delivery_type');
            $table->string('payment_method');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('delivery_price', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('status')->default('new');
            $table->timestamp('stock_returned_at')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
