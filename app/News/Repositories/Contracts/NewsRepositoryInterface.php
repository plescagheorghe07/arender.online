<?php

namespace App\News\Repositories\Contracts;

use App\News\Models\NewsArticle;
use Illuminate\Support\Collection;

interface NewsRepositoryInterface
{
    /**
     * @return Collection<int, NewsArticle>
     */
    public function published(): Collection;

    public function findPublishedBySlug(string $slug): ?NewsArticle;
}
