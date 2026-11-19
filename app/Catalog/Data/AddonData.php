<?php

namespace App\Catalog\Data;

readonly class AddonData
{
    public function __construct(
        public int $id,
        public string $title,
        public int $price,
        public ?string $imageUrl,
    ) {}
}
