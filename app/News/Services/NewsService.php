<?php

namespace App\News\Services;

use App\News\Data\ArticleData;
use App\News\Models\NewsArticle;
use App\News\Repositories\Contracts\NewsRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class NewsService
{
    public function __construct(
        private readonly NewsRepositoryInterface $articles,
    ) {}

    /**
     * @return Collection<int, ArticleData>
     */
    public function all(): Collection
    {
        return $this->articles
            ->published()
            ->map(fn (NewsArticle $article): ArticleData => $this->present($article));
    }

    public function find(string $slug): ArticleData
    {
        $article = $this->articles->findPublishedBySlug($slug);

        if ($article === null) {
            throw (new ModelNotFoundException)->setModel(NewsArticle::class, [$slug]);
        }

        return $this->present($article);
    }

    private function present(NewsArticle $article): ArticleData
    {
        $russian = app()->getLocale() === 'ru';

        return new ArticleData(
            slug: $article->slug,
            title: $russian ? $article->title_ru : $article->title_ro,
            body: $russian ? $article->body_ru : $article->body_ro,
            date: $article->published_on->isoFormat('LL'),
            publishedOn: $article->published_on->toDateString(),
            imageUrls: $article->images
                ->map(fn ($image): ?string => $image->path ? Storage::disk('public')->url($image->path) : null)
                ->filter()
                ->values()
                ->all(),
        );
    }
}
