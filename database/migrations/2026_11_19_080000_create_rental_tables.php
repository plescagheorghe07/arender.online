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
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description_ro')->nullable();
            $table->text('description_ru')->nullable();
            $table->text('details_ro')->nullable();
            $table->text('details_ru')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('days_label', 20);
            $table->unsignedInteger('price');
            $table->boolean('is_best')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['product_id', 'sort_order']);
        });

        Schema::create('product_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('title_ro');
            $table->string('title_ru');
            $table->unsignedInteger('price');
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::create('news_articles', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title_ro');
            $table->string('title_ru');
            $table->text('body_ro');
            $table->text('body_ru');
            $table->date('published_on');
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['is_published', 'published_on']);
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('days_label', 20);
            $table->unsignedInteger('price');
            $table->json('addons');
            $table->string('customer_name');
            $table->string('phone', 40);
            $table->string('address');
            $table->text('note')->nullable();
            $table->string('promo_code', 40)->nullable();
            $table->string('status', 20)->default('new');
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();
            $table->string('label');
            $table->boolean('is_active')->default(true);
            $table->boolean('for_game')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('news_articles');
        Schema::dropIfExists('product_addons');
        Schema::dropIfExists('product_prices');
        Schema::dropIfExists('products');
    }
};
