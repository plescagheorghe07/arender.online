<?php

namespace App\Catalog\Data;

readonly class PriceData
{
    public function __construct(
        public string $days,
        public int $price,
        public bool $isBest,
    ) {}
}
