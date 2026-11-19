<?php

namespace App\News\Repositories;

use App\News\Models\NewsArticle;
use App\News\Repositories\Contracts\NewsRepositoryInterface;
use Illuminate\Support\Collection;

class NewsRepository implements NewsRepositoryInterface
{
    public function published(): Collection
    {
        return NewsArticle::query()
            ->published()
            ->with('images')
            ->orderBy('sort_order')
            ->orderByDesc('published_on')
            ->get();
    }

    public function findPublishedBySlug(string $slug): ?NewsArticle
    {
        return NewsArticle::query()->published()->with('images')->where('slug', $slug)->first();
    }
}
