<?php

use App\Admin\Controllers\CustomerController;
use App\Admin\Controllers\DashboardController;
use App\Admin\Controllers\NewsController as AdminNewsController;
use App\Admin\Controllers\OrderController as AdminOrderController;
use App\Admin\Controllers\ProductController as AdminProductController;
use App\Admin\Controllers\PromoController;
use App\Admin\Controllers\SessionController;
use App\Admin\Controllers\UserController;
use App\Catalog\Controllers\ProductController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SitemapController;
use App\News\Controllers\NewsController;
use App\Orders\Controllers\OrderController;
use App\Promos\Controllers\MinigameController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $locale = session('locale');
    $supported = config('locales.supported', []);

    if (! is_string($locale) || ! in_array($locale, $supported, true)) {
        $locale = (string) config('app.locale');
    }

    return redirect()->route('home', ['locale' => $locale]);
})->name('root');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('/robots.txt', function () {
    $lines = [
        'User-agent: *',
        'Allow: /',
        'Disallow: /admin',
        'Sitemap: '.route('sitemap'),
    ];

    return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
})->name('robots');

Route::prefix('{locale}')
    ->whereIn('locale', config('locales.supported'))
    ->group(function (): void {
        Route::get('/', [PageController::class, 'home'])->name('home');
        Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');

        Route::prefix('catalog')->name('catalog.')->group(function (): void {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/{product}', [ProductController::class, 'show'])
                ->name('show')
                ->where('product', '[a-z0-9\-]+');
            Route::post('/{product}/orders', [OrderController::class, 'store'])
                ->name('orders.store')
                ->where('product', '[a-z0-9\-]+')
                ->middleware('throttle:8,1');
        });

        Route::prefix('news')->name('news.')->group(function (): void {
            Route::get('/', [NewsController::class, 'index'])->name('index');
            Route::get('/{article}', [NewsController::class, 'show'])
                ->name('show')
                ->where('article', '[a-z0-9\-]+');
        });

        Route::get('/minigame', [MinigameController::class, 'show'])->name('minigame');
        Route::post('/minigame/reward', [MinigameController::class, 'reward'])
            ->name('minigame.reward')
            ->middleware('throttle:10,1');
    });

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('/login', [SessionController::class, 'create'])->name('login');
        Route::post('/login', [SessionController::class, 'store'])
            ->name('login.store')
            ->middleware('throttle:5,1');
    });

    Route::middleware('role:admin,manager,support')->group(function (): void {
        Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
        Route::patch('/orders/{order}/details', [AdminOrderController::class, 'updateDetails'])->name('orders.details');
        Route::post('/orders/{order}/notes', [AdminOrderController::class, 'storeNote'])->name('orders.notes.store');
        Route::post('/orders/{order}/customer', [AdminOrderController::class, 'attachCustomer'])->name('orders.customer.attach');
        Route::delete('/orders/{order}/customer', [AdminOrderController::class, 'detachCustomer'])->name('orders.customer.detach');
        Route::post('/orders/{order}/customer/new', [AdminOrderController::class, 'createCustomer'])->name('orders.customer.create');

        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
        Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
        Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    });

    Route::middleware('role:admin,manager')->group(function (): void {
        Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
        Route::post('/products/reorder', [AdminProductController::class, 'reorder'])->name('products.reorder');
        Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
        Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');

        Route::get('/news', [AdminNewsController::class, 'index'])->name('news.index');
        Route::post('/news/reorder', [AdminNewsController::class, 'reorder'])->name('news.reorder');
        Route::get('/news/create', [AdminNewsController::class, 'create'])->name('news.create');
        Route::post('/news', [AdminNewsController::class, 'store'])->name('news.store');
        Route::get('/news/{article}/edit', [AdminNewsController::class, 'edit'])->name('news.edit');
        Route::put('/news/{article}', [AdminNewsController::class, 'update'])->name('news.update');
        Route::delete('/news/{article}', [AdminNewsController::class, 'destroy'])->name('news.destroy');

        Route::get('/promos', [PromoController::class, 'index'])->name('promos.index');
        Route::post('/promos', [PromoController::class, 'store'])->name('promos.store');
        Route::put('/promos/{promo}', [PromoController::class, 'update'])->name('promos.update');
        Route::delete('/promos/{promo}', [PromoController::class, 'destroy'])->name('promos.destroy');
    });

    Route::middleware('role:admin')->group(function (): void {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
