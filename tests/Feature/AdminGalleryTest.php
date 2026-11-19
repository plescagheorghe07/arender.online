<?php

namespace Tests\Feature;

use App\Catalog\Models\Product;
use App\Models\User;
use App\News\Models\NewsArticle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_product_keeps_several_photos_and_the_list_order_can_be_dragged(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Consolă',
            'description_ro' => 'Descriere',
            'description_ru' => 'Описание',
            'is_active' => '1',
            'images' => [
                UploadedFile::fake()->image('unu.jpg'),
                UploadedFile::fake()->image('doi.jpg'),
            ],
        ])->assertRedirect();

        $product = Product::query()->where('name', 'Consolă')->firstOrFail();
        $this->assertCount(2, $product->images);
        $this->assertSame($product->images()->orderBy('sort_order')->value('path'), $product->fresh()->image);

        $second = Product::query()->create([
            'name' => 'Aspirator',
            'slug' => 'aspirator',
            'description_ro' => '',
            'description_ru' => '',
            'details_ro' => '',
            'details_ru' => '',
            'is_active' => true,
            'sort_order' => 4,
        ]);
        $product->update(['sort_order' => 9]);

        $this->actingAs($admin)->postJson(route('admin.products.reorder'), [
            'ids' => [$second->id, $product->id],
        ])->assertOk();

        $this->assertSame(
            [$second->id, $product->id],
            Product::query()->orderBy('sort_order')->pluck('id')->all(),
        );
    }

    public function test_a_news_article_stores_photos_and_accepts_a_new_order(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.news.store'), [
            'title_ro' => 'Titlu',
            'title_ru' => 'Заголовок',
            'body_ro' => 'Text',
            'body_ru' => 'Текст',
            'published_on' => '2026-11-19',
            'is_published' => '1',
            'images' => [UploadedFile::fake()->image('stire.jpg')],
        ])->assertRedirect();

        $article = NewsArticle::query()->where('title_ro', 'Titlu')->firstOrFail();
        $this->assertCount(1, $article->images);

        $other = NewsArticle::query()->create([
            'slug' => 'alta',
            'title_ro' => 'Alta',
            'title_ru' => 'Другая',
            'body_ro' => 'Text',
            'body_ru' => 'Текст',
            'published_on' => '2026-11-18',
            'is_published' => true,
            'sort_order' => 5,
        ]);

        $this->actingAs($admin)->postJson(route('admin.news.reorder'), [
            'ids' => [$other->id, $article->id],
        ])->assertOk();

        $this->assertSame(0, $other->fresh()->sort_order);
        $this->assertSame(1, $article->fresh()->sort_order);
    }

    public function test_accessory_titles_use_the_romanian_and_russian_tabs(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::query()->create([
            'name' => 'Consolă',
            'slug' => 'consola',
            'description_ro' => 'Descriere',
            'description_ru' => 'Описание',
            'details_ro' => '',
            'details_ru' => '',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $product->addons()->create([
            'title_ro' => 'Proiector',
            'title_ru' => 'Проектор',
            'price' => 200,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.products.edit', $product))
            ->assertOk()
            ->assertSee('data-record-tab="addons"', false)
            ->assertSee('name="addons[0][title_ro]"', false)
            ->assertSee('name="addons[0][title_ru]"', false)
            ->assertSee('data-tab-panel="ru"', false);
    }
}
