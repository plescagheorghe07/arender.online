<?php

namespace App\Admin\Services;

use App\Media\Services\GalleryService;
use App\News\Models\NewsArticle;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsAdminService
{
    public function __construct(
        private readonly GalleryService $gallery,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, UploadedFile|null>  $uploads
     * @param  list<int|string>  $keepIds
     */
    public function save(?NewsArticle $article, array $data, array $uploads = [], array $keepIds = []): NewsArticle
    {
        $article ??= new NewsArticle;
        $article->fill([
            'title_ro' => $data['title_ro'],
            'title_ru' => $data['title_ru'],
            'body_ro' => $data['body_ro'],
            'body_ru' => $data['body_ru'],
            'published_on' => $data['published_on'],
            'is_published' => (bool) ($data['is_published'] ?? false),
            'slug' => $this->slug($data['slug'] ?? '', $data['title_ro'], $article->id),
            'sort_order' => $article->exists
                ? $article->sort_order
                : ((int) NewsArticle::query()->max('sort_order')) + 1,
        ]);
        $article->save();
        $this->gallery->sync($article, $keepIds, $uploads);

        return $article;
    }

    /**
     * @param  list<int>  $ids
     */
    public function reorder(array $ids): void
    {
        foreach (array_values($ids) as $index => $id) {
            NewsArticle::query()->whereKey($id)->update(['sort_order' => $index]);
        }
    }

    public function delete(NewsArticle $article): void
    {
        $article->load('images');
        $article->images->each(function ($image): void {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        });
        $article->delete();
    }

    private function slug(string $requested, string $title, ?int $ignoreId): string
    {
        $base = Str::slug($requested !== '' ? $requested : $title);
        $base = $base !== '' ? Str::limit($base, 80, '') : 'stire';
        $slug = $base;
        $suffix = 2;

        while (NewsArticle::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
