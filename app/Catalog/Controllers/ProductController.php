<?php

namespace App\Catalog\Controllers;

use App\Catalog\Services\CatalogService;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class ProductController extends Controller
{
    public function __construct(
        private readonly CatalogService $catalog,
    ) {}

    public function index(): View
    {
        return view('catalog.index', [
            'products' => $this->catalog->all(),
        ]);
    }

    public function show(string $locale, string $product): View
    {
        return view('catalog.show', [
            'product' => $this->catalog->find($product),
        ]);
    }
}
