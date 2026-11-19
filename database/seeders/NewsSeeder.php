<?php

namespace Database\Seeders;

use App\News\Models\NewsArticle;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $rows = json_decode(
            (string) file_get_contents(database_path('seeders/data/news.json')),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        foreach ($rows as $row) {
            NewsArticle::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'title_ro' => $row['title_ro'],
                    'title_ru' => $row['title_ru'],
                    'body_ro' => $row['body_ro'],
                    'body_ru' => $row['body_ru'],
                    'published_on' => $row['published_on'],
                    'is_published' => true,
                ],
            );
        }
    }
}
