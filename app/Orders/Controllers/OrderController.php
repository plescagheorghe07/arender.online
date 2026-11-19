<?php

namespace App\Orders\Controllers;

use App\Catalog\Services\CatalogService;
use App\Http\Controllers\Controller;
use App\Orders\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private readonly CatalogService $catalog,
        private readonly OrderService $orders,
    ) {}

    public function store(Request $request, string $locale, string $product): RedirectResponse
    {
        $order = $this->orders->place($this->catalog->model($product), $request->all());

        return redirect()
            ->route('catalog.show', $product)
            ->with('status', __('ui.order_sent', [
                'total' => number_format($order->price, 0, ',', ' ').' MDL',
            ]));
    }
}
