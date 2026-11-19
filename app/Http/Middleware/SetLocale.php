<?php

namespace App\Http\Middleware;

use App\Locales\Services\LocaleService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function __construct(
        private readonly LocaleService $locales,
    ) {}

    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeLocale = $request->route('locale');

        if (is_string($routeLocale) && $this->locales->supports($routeLocale)) {
            $this->locales->remember($routeLocale);
        } else {
            $this->locales->apply($request->session()->get('locale'));
        }

        URL::defaults(['locale' => app()->getLocale()]);

        return $next($request);
    }
}
