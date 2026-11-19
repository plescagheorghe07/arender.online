<?php

namespace App\Catalog\Repositories\Contracts;

use App\Catalog\Models\Product;
use Illuminate\Support\Collection;

interface CatalogRepositoryInterface
{
    /**
     * @return Collection<int, Product>
     */
    public function active(): Collection;

    public function findActiveBySlug(string $slug): ?Product;

    public function flush(): void;
}
