<?php

namespace App\Providers;

use App\Catalog\Repositories\CatalogRepository;
use App\Catalog\Repositories\Contracts\CatalogRepositoryInterface;
use App\News\Repositories\Contracts\NewsRepositoryInterface;
use App\News\Repositories\NewsRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CatalogRepositoryInterface::class, CatalogRepository::class);
        $this->app->bind(NewsRepositoryInterface::class, NewsRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! $this->app->isProduction());
        DB::prohibitDestructiveCommands($this->app->isProduction());

        Paginator::defaultView('partials.pagination');
        URL::defaults(['locale' => (string) config('app.locale')]);
    }
}
