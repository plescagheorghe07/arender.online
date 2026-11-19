<?php

namespace App\Http\Controllers;

use App\Catalog\Services\CatalogService;
use App\News\Services\NewsService;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function __construct(
        private readonly CatalogService $catalog,
        private readonly NewsService $articles,
    ) {}

    public function home(string $locale): View
    {
        return view('pages.home', [
            'products' => $this->catalog->featured(),
            'articles' => $this->articles->all()->take(3),
        ]);
    }

    public function privacy(string $locale): View
    {
        return view('pages.privacy');
    }
}
