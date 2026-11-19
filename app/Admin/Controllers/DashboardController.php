<?php

namespace App\Admin\Controllers;

use App\Catalog\Models\Product;
use App\Http\Controllers\Controller;
use App\News\Models\NewsArticle;
use App\Orders\Models\Order;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'products' => Product::query()->count(),
            'articles' => NewsArticle::query()->count(),
            'newOrders' => Order::query()->where('status', 'new')->count(),
            'orders' => Order::query()->latest()->limit(5)->get(),
        ]);
    }
}
