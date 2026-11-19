<?php

namespace App\Locales\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;

class LocaleService
{
    /**
     * @return list<string>
     */
    public function supported(): array
    {
        return config('locales.supported', []);
    }

    /**
     * @return array<string, string>
     */
    public function available(): array
    {
        return config('locales.available', []);
    }

    public function supports(string $locale): bool
    {
        return in_array($locale, $this->supported(), true);
    }

    public function apply(?string $locale): void
    {
        $resolved = is_string($locale) && $this->supports($locale)
            ? $locale
            : (string) config('app.locale');

        App::setLocale($resolved);
        Carbon::setLocale($resolved);

        View::share('currentLocale', $resolved);
        View::share('locales', $this->available());
    }

    public function remember(string $locale): void
    {
        if (! $this->supports($locale)) {
            abort(404);
        }

        session(['locale' => $locale]);

        $this->apply($locale);
    }
}
