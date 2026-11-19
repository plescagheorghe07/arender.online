<?php

namespace Database\Seeders;

use App\Catalog\Models\Product;
use App\Catalog\Repositories\Contracts\CatalogRepositoryInterface;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $rows = json_decode(
            (string) file_get_contents(database_path('seeders/data/catalog.json')),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        foreach ($rows as $row) {
            $this->publish($row['image']);

            $product = Product::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'description_ro' => $row['description_ro'],
                    'description_ru' => $row['description_ru'],
                    'details_ro' => $row['details_ro'],
                    'details_ru' => $row['details_ru'],
                    'image' => $row['image'],
                    'is_active' => true,
                    'sort_order' => $row['sort_order'],
                ],
            );

            $product->prices()->delete();
            $product->prices()->createMany($row['prices'] ? array_map(
                fn (array $price): array => [
                    'days_label' => $price['days'],
                    'price' => $price['price'],
                    'is_best' => $price['is_best'],
                    'sort_order' => $price['sort_order'],
                ],
                $row['prices'],
            ) : []);

            $product->addons()->delete();
            foreach ($row['addons'] as $addon) {
                if ($addon['image']) {
                    $this->publish($addon['image']);
                }

                $product->addons()->create([
                    'title_ro' => $addon['title_ro'],
                    'title_ru' => $addon['title_ru'],
                    'price' => $addon['price'],
                    'image' => $addon['image'],
                ]);
            }
        }

        app(CatalogRepositoryInterface::class)->flush();
    }

    private function publish(?string $relative): void
    {
        if ($relative === null || $relative === '') {
            return;
        }

        $source = database_path('seeders/data/images/'.$relative);

        if (is_file($source)) {
            Storage::disk('public')->put($relative, (string) file_get_contents($source));
        }
    }
}
