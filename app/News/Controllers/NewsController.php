<?php

namespace App\News\Controllers;

use App\Http\Controllers\Controller;
use App\News\Services\NewsService;
use Illuminate\Contracts\View\View;

class NewsController extends Controller
{
    public function __construct(
        private readonly NewsService $news,
    ) {}

    public function index(): View
    {
        return view('news.index', [
            'articles' => $this->news->all(),
        ]);
    }

    public function show(string $locale, string $article): View
    {
        return view('news.show', [
            'article' => $this->news->find($article),
        ]);
    }
}
