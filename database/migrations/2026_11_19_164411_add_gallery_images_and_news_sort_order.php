<?php

use App\Catalog\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_images', function (Blueprint $table): void {
            $table->id();
            $table->morphs('imageable');
            $table->string('path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('news_articles', function (Blueprint $table): void {
            $table->unsignedInteger('sort_order')->default(0);
        });

        $now = now();

        foreach (DB::table('products')->whereNotNull('image')->where('image', '!=', '')->orderBy('id')->get() as $product) {
            DB::table('gallery_images')->insert([
                'imageable_type' => Product::class,
                'imageable_id' => $product->id,
                'path' => $product->image,
                'sort_order' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        foreach (DB::table('news_articles')->orderByDesc('published_on')->orderByDesc('id')->get() as $index => $article) {
            DB::table('news_articles')->where('id', $article->id)->update(['sort_order' => $index]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_images');

        Schema::table('news_articles', function (Blueprint $table): void {
            $table->dropColumn('sort_order');
        });
    }
};
