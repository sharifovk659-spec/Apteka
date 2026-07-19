<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();
            $table->foreignId('brand_id')
                ->nullable()
                ->constrained('brands')
                ->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->string('barcode')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->text('composition')->nullable();
            $table->text('usage_instructions')->nullable();
            $table->text('contraindications')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('country')->nullable();
            $table->string('dosage_form')->nullable();
            $table->string('dosage')->nullable();
            $table->boolean('requires_prescription')->default(false);
            $table->decimal('price', 12, 2);
            $table->decimal('old_price', 12, 2)->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->string('status')->default('published');
            $table->string('main_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_daily_product')->default(false);
            $table->boolean('is_bestseller')->default(false);
            $table->timestamps();

            $table->index('category_id');
            $table->index('brand_id');
            $table->index('barcode');
            $table->index('is_active');
            $table->index('status');
            $table->index('is_featured');
            $table->index(['is_active', 'is_featured']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
