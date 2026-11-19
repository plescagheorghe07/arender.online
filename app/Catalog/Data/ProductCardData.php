<?php

namespace App\Catalog\Data;

readonly class ProductCardData
{
    public function __construct(
        public string $slug,
        public string $name,
        public string $summary,
        public ?string $imageUrl,
        public ?int $fromPrice,
    ) {}
}
