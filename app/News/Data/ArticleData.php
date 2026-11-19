<?php

namespace App\News\Data;

readonly class ArticleData
{
    public function __construct(
        public string $slug,
        public string $title,
        public string $body,
        public string $date,
        public string $publishedOn,
        /** @var list<string> */
        public array $imageUrls = [],
    ) {}
}
