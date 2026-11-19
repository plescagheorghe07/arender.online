<?php

namespace App\Catalog\Data;

readonly class ProductDetailData
{
    /**
     * @param  list<string>  $imageUrls
     * @param  list<PriceData>  $prices
     * @param  list<AddonData>  $addons
     */
    public function __construct(
        public string $slug,
        public string $name,
        public string $description,
        public string $details,
        public ?string $imageUrl,
        public array $imageUrls,
        public array $prices,
        public array $addons,
    ) {}
}
