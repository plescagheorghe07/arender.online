<?php

namespace App\Catalog\Repositories;

use App\Catalog\Models\Product;
use App\Catalog\Repositories\Contracts\CatalogRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CatalogRepository implements CatalogRepositoryInterface
{
    public const CACHE_KEY = 'catalog.active';

    public function active(): Collection
    {
        return Product::query()
            ->active()
            ->with(['prices', 'addons', 'images'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function findActiveBySlug(string $slug): ?Product
    {
        return $this->active()->firstWhere('slug', $slug);
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
