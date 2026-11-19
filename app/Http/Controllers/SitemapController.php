<?php

namespace App\Http\Controllers;

use App\Catalog\Models\Product;
use App\News\Models\NewsArticle;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $locales = config('locales.supported', ['ro']);
        $urls = [];

        foreach ($locales as $locale) {
            $urls[] = ['loc' => route('home', ['locale' => $locale]), 'changefreq' => 'daily'];
            $urls[] = ['loc' => route('privacy', ['locale' => $locale]), 'changefreq' => 'yearly'];
            $urls[] = ['loc' => route('catalog.index', ['locale' => $locale]), 'changefreq' => 'daily'];
            $urls[] = ['loc' => route('news.index', ['locale' => $locale]), 'changefreq' => 'weekly'];

            foreach (Product::query()->active()->orderBy('sort_order')->get() as $product) {
                $urls[] = [
                    'loc' => route('catalog.show', ['locale' => $locale, 'product' => $product]),
                    'changefreq' => 'weekly',
                ];
            }

            foreach (NewsArticle::query()->published()->orderByDesc('published_on')->get() as $article) {
                $urls[] = [
                    'loc' => route('news.show', ['locale' => $locale, 'article' => $article]),
                    'changefreq' => 'monthly',
                ];
            }
        }

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
