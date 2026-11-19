<?php

namespace App\Admin\Services;

use App\Catalog\Models\Product;
use App\Catalog\Models\ProductAddon;
use App\Catalog\Repositories\Contracts\CatalogRepositoryInterface;
use App\Media\Services\GalleryService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductAdminService
{
    public function __construct(
        private readonly CatalogRepositoryInterface $catalog,
        private readonly GalleryService $gallery,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, UploadedFile|null>  $uploads
     * @param  list<int|string>  $keepIds
     * @param  array<int, UploadedFile|null>  $addonImages
     */
    public function save(?Product $product, array $data, array $uploads, array $keepIds, array $addonImages): Product
    {
        $product = DB::transaction(function () use ($product, $data, $uploads, $keepIds, $addonImages): Product {
            $product ??= new Product;
            $product->fill([
                'name' => $data['name'],
                'slug' => $this->slug($data['slug'] ?? '', $data['name'], $product->id),
                'description_ro' => $data['description_ro'] ?? '',
                'description_ru' => $data['description_ru'] ?? '',
                'details_ro' => $data['details_ro'] ?? '',
                'details_ru' => $data['details_ru'] ?? '',
                'is_active' => (bool) ($data['is_active'] ?? false),
                'sort_order' => $product->exists
                    ? $product->sort_order
                    : ((int) Product::query()->max('sort_order')) + 1,
            ]);

            $product->save();
            $this->gallery->sync($product, $keepIds, $uploads);

            $product->prices()->delete();
            foreach (array_values($data['prices'] ?? []) as $index => $row) {
                if (trim((string) ($row['days'] ?? '')) === '') {
                    continue;
                }

                $product->prices()->create([
                    'days_label' => trim((string) $row['days']),
                    'price' => (int) $row['price'],
                    'is_best' => (bool) ($row['is_best'] ?? false),
                    'sort_order' => $index,
                ]);
            }

            $kept = [];
            foreach (array_values($data['addons'] ?? []) as $index => $row) {
                $titleRo = trim((string) ($row['title_ro'] ?? ''));
                $titleRu = trim((string) ($row['title_ru'] ?? ''));
                if ($titleRo === '' && $titleRu === '') {
                    continue;
                }

                $addon = isset($row['id'])
                    ? $product->addons()->whereKey($row['id'])->first()
                    : null;
                $addon ??= new ProductAddon(['product_id' => $product->id]);
                $addon->fill([
                    'title_ro' => $titleRo !== '' ? $titleRo : $titleRu,
                    'title_ru' => $titleRu !== '' ? $titleRu : $titleRo,
                    'price' => (int) ($row['price'] ?? 0),
                ]);

                $upload = $addonImages[$index] ?? null;
                if ($upload instanceof UploadedFile) {
                    $this->deleteFile($addon->image);
                    $addon->image = $upload->store('addons', 'public');
                }

                $addon->save();
                $kept[] = $addon->id;
            }

            $product->addons()->whereNotIn('id', $kept)->get()->each(function (ProductAddon $addon): void {
                $this->deleteFile($addon->image);
                $addon->delete();
            });

            return $product;
        });

        $this->catalog->flush();

        return $product;
    }

    /**
     * @param  list<int>  $ids
     */
    public function reorder(array $ids): void
    {
        foreach (array_values($ids) as $index => $id) {
            Product::query()->whereKey($id)->update(['sort_order' => $index]);
        }

        $this->catalog->flush();
    }

    public function delete(Product $product): void
    {
        $product->load('images');
        $product->images->each(function ($image): void {
            $this->deleteFile($image->path);
            $image->delete();
        });
        $this->deleteFile($product->image);
        $product->addons()->get()->each(fn (ProductAddon $addon) => $this->deleteFile($addon->image));
        $product->delete();
        $this->catalog->flush();
    }

    private function slug(string $requested, string $name, ?int $ignoreId): string
    {
        $base = Str::slug($requested !== '' ? $requested : $name);
        $base = $base !== '' ? $base : 'produs';
        $slug = $base;
        $suffix = 2;

        while (Product::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    private function deleteFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
