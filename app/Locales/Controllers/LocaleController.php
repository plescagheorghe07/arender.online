<?php

namespace App\Locales\Controllers;

use App\Http\Controllers\Controller;
use App\Locales\Services\LocaleService;
use Illuminate\Http\RedirectResponse;

class LocaleController extends Controller
{
    public function __construct(
        private readonly LocaleService $locales,
    ) {}

    public function update(string $locale): RedirectResponse
    {
        $this->locales->remember($locale);

        return redirect()->back(fallback: route('home'));
    }
}
