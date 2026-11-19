<?php

namespace App\Catalog\Services;

use App\Catalog\Data\AddonData;
use App\Catalog\Data\PriceData;
use App\Catalog\Data\ProductCardData;
use App\Catalog\Data\ProductDetailData;
use App\Catalog\Models\Product;
use App\Catalog\Models\ProductAddon;
use App\Catalog\Models\ProductPrice;
use App\Catalog\Repositories\Contracts\CatalogRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class CatalogService
{
    public const FEATURED_LIMIT = 6;

    public function __construct(
        private readonly CatalogRepositoryInterface $catalog,
    ) {}

    /**
     * @return Collection<int, ProductCardData>
     */
    public function featured(): Collection
    {
        return $this->catalog
            ->active()
            ->take(self::FEATURED_LIMIT)
            ->map(fn (Product $product): ProductCardData => $this->card($product));
    }

    /**
     * @return Collection<int, ProductCardData>
     */
    public function all(): Collection
    {
        return $this->catalog
            ->active()
            ->map(fn (Product $product): ProductCardData => $this->card($product));
    }

    public function find(string $slug): ProductDetailData
    {
        $product = $this->catalog->findActiveBySlug($slug);

        if ($product === null) {
            throw (new ModelNotFoundException)->setModel(Product::class, [$slug]);
        }

        return $this->detail($product);
    }

    public function model(string $slug): Product
    {
        $product = $this->catalog->findActiveBySlug($slug);

        if ($product === null) {
            throw (new ModelNotFoundException)->setModel(Product::class, [$slug]);
        }

        return $product;
    }

    private function card(Product $product): ProductCardData
    {
        $description = $this->localized($product->description_ro, $product->description_ru);
        $details = $this->localized($product->details_ro, $product->details_ru);

        return new ProductCardData(
            slug: $product->slug,
            name: $product->name,
            summary: $description !== '' ? $description : $details,
            imageUrl: $this->url($product->image),
            fromPrice: $product->prices->min('price'),
        );
    }

    private function detail(Product $product): ProductDetailData
    {
        return new ProductDetailData(
            slug: $product->slug,
            name: $product->name,
            description: $this->localized($product->description_ro, $product->description_ru),
            details: $this->localized($product->details_ro, $product->details_ru),
            imageUrl: $this->url($product->image),
            imageUrls: $this->imageUrls($product),
            prices: $product->prices
                ->map(fn (ProductPrice $price): PriceData => new PriceData(
                    days: $price->days_label,
                    price: $price->price,
                    isBest: $price->is_best,
                ))
                ->all(),
            addons: $product->addons
                ->map(fn (ProductAddon $addon): AddonData => new AddonData(
                    id: $addon->id,
                    title: $this->localized($addon->title_ro, $addon->title_ru),
                    price: $addon->price,
                    imageUrl: $this->url($addon->image),
                ))
                ->all(),
        );
    }

    /**
     * @return list<string>
     */
    private function imageUrls(Product $product): array
    {
        $urls = $product->images
            ->map(fn ($image): ?string => $this->url($image->path))
            ->filter()
            ->values()
            ->all();

        if ($urls !== []) {
            return $urls;
        }

        $cover = $this->url($product->image);

        return $cover === null ? [] : [$cover];
    }

    private function localized(?string $romanian, ?string $russian): string
    {
        $primary = app()->getLocale() === 'ru' ? $russian : $romanian;
        $fallback = app()->getLocale() === 'ru' ? $romanian : $russian;
        $value = trim((string) $primary);

        return $value !== '' ? $value : trim((string) $fallback);
    }

    private function url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        return Storage::disk('public')->url($path);
    }
}
