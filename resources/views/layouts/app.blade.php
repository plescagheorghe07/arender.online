<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @php
            $seoTitle = trim($__env->yieldContent('title'));
            $seoTitleFull = $seoTitle !== '' ? $seoTitle.' · '.config('app.name') : config('app.name');
            $seoDescription = trim($__env->yieldContent('description')) ?: __('ui.seo_home_description');
            $seoImage = trim($__env->yieldContent('og_image')) ?: asset('images/home-gamers.jpg');
        @endphp
        <title>{{ $seoTitle !== '' ? $seoTitleFull : __('ui.seo_home_title') }}</title>
        <meta name="description" content="{{ $seoDescription }}">
        <meta name="keywords" content="{{ __('ui.seo_keywords') }}">
        <meta name="robots" content="@yield('robots', 'index, follow, max-image-preview:large')">
        <meta name="author" content="{{ config('company.legal_name') }}">
        <meta name="geo.region" content="MD-CU">
        <meta name="geo.placename" content="{{ config('company.city') }}">
        <meta name="geo.position" content="47.0465;28.8649">
        <meta name="ICBM" content="47.0465, 28.8649">
        <link rel="canonical" href="{{ url()->current() }}">
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <link rel="icon" href="{{ asset('favicon-32.png') }}" type="image/png" sizes="32x32">
        <link rel="icon" href="{{ asset('favicon-16.png') }}" type="image/png" sizes="16x16">
        <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}" sizes="180x180">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">
        <meta name="theme-color" content="#2563eb">
        @if (request()->route('locale'))
            @foreach ($locales ?? [] as $code => $native)
                <link rel="alternate" hreflang="{{ $code }}" href="{{ route(request()->route()->getName(), array_merge(request()->route()->parameters(), ['locale' => $code])) }}">
            @endforeach
            <link rel="alternate" hreflang="x-default" href="{{ route(request()->route()->getName(), array_merge(request()->route()->parameters(), ['locale' => config('app.locale')])) }}">
        @endif
        <meta property="og:site_name" content="{{ config('app.name') }}">
        <meta property="og:title" content="{{ $seoTitle !== '' ? $seoTitleFull : __('ui.seo_home_title') }}">
        <meta property="og:description" content="{{ $seoDescription }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:type" content="@yield('og_type', 'website')">
        <meta property="og:locale" content="{{ app()->getLocale() === 'ru' ? 'ru_RU' : 'ro_RO' }}">
        <meta property="og:locale:alternate" content="{{ app()->getLocale() === 'ru' ? 'ro_RO' : 'ru_RU' }}">
        <meta property="og:image" content="{{ $seoImage }}">
        <meta property="og:image:alt" content="{{ __('ui.home_image') }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $seoTitle !== '' ? $seoTitleFull : __('ui.seo_home_title') }}">
        <meta name="twitter:description" content="{{ $seoDescription }}">
        <meta name="twitter:image" content="{{ $seoImage }}">
        @stack('head')
        <script>
            if (window.matchMedia('(prefers-reduced-motion: no-preference)').matches) {
                document.documentElement.classList.add('motion');
            }
        </script>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @fonts
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="site flex min-h-screen flex-col text-stone-900 antialiased">
        <a href="#content" class="sr-only focus:not-sr-only focus:absolute focus:top-3 focus:left-3 focus:z-50 focus:rounded-full focus:bg-stone-900 focus:px-4 focus:py-2 focus:text-sm focus:text-white">
            {{ __('ui.skip') }}
        </a>

        <div class="h-1 bg-gradient-to-r from-blue-600 via-cyan-400 to-violet-400"></div>

        <header class="sticky top-0 z-20 border-b border-white/80 bg-white/90 shadow-[0_10px_30px_rgba(37,99,235,0.06)] backdrop-blur-xl">
            @php
                $siteNav = [
                    ['route' => 'catalog.index', 'active' => request()->routeIs('catalog.*'), 'label' => __('ui.nav_catalog'), 'icon' => 'grid'],
                    ['route' => 'news.index', 'active' => request()->routeIs('news.*'), 'label' => __('ui.nav_news'), 'icon' => 'news'],
                    ['route' => 'minigame', 'active' => request()->routeIs('minigame'), 'label' => __('ui.nav_minigame'), 'icon' => 'game'],
                ];
            @endphp
            <div class="mx-auto w-full max-w-5xl px-4 sm:px-5">
                <div class="flex items-center gap-3 py-3">
                    <a href="{{ route('home') }}" class="shrink-0 text-base font-semibold tracking-tight">
                        {{ config('app.name') }}
                    </a>

                    <nav aria-label="{{ __('ui.menu') }}" class="hidden min-w-0 flex-1 items-center gap-5 sm:flex">
                        @foreach ($siteNav as $item)
                            <a href="{{ route($item['route']) }}" @class(['site-link inline-flex items-center gap-1.5 text-sm font-medium', 'text-blue-700' => $item['active'], 'text-stone-600 hover:text-stone-950' => ! $item['active']]) @if ($item['active']) aria-current="page" @endif>
                                <x-site.icon :name="$item['icon']" class="h-4 w-4" />
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </nav>

                    <div class="ml-auto flex shrink-0 items-center gap-2">
                        <a href="tel:{{ config('company.phone') }}" class="grid h-10 w-10 place-items-center rounded-full bg-blue-600 text-white shadow-sm shadow-blue-900/20 sm:hidden" aria-label="{{ config('company.phone_display') }}">
                            <x-site.icon name="phone" class="h-4 w-4" />
                        </a>
                        <a href="tel:{{ config('company.phone') }}" class="hidden items-center gap-1.5 text-sm font-medium text-blue-700 sm:inline-flex">
                            <x-site.icon name="phone" class="h-4 w-4" />
                            {{ config('company.phone_display') }}
                        </a>
                        <nav aria-label="{{ __('ui.languages') }}" class="flex items-center gap-0.5 rounded-full bg-white p-1 shadow-sm">
                            @foreach ($locales ?? [] as $code => $native)
                                <a
                                    href="{{ request()->route('locale') ? route(request()->route()->getName(), array_merge(request()->route()->parameters(), ['locale' => $code])) : route('home', ['locale' => $code]) }}"
                                    hreflang="{{ $code }}"
                                    lang="{{ $code }}"
                                    @class([
                                        'rounded-full px-2.5 py-1 text-sm sm:px-3',
                                        'bg-blue-600 text-white' => $code === ($currentLocale ?? app()->getLocale()),
                                        'text-stone-600 hover:text-stone-950' => $code !== ($currentLocale ?? app()->getLocale()),
                                    ])
                                    @if ($code === ($currentLocale ?? app()->getLocale())) aria-current="true" @endif
                                ><span class="sm:hidden">{{ strtoupper($code) }}</span><span class="hidden sm:inline">{{ $native }}</span></a>
                            @endforeach
                        </nav>
                    </div>
                </div>

            </div>
        </header>

        <main id="content" class="mx-auto w-full max-w-5xl flex-1 px-5 py-12 sm:py-16">
            @if (session('status'))
                <p class="mb-8 rounded-2xl bg-sky-50 px-4 py-3 text-sm text-blue-950 shadow-sm">{{ session('status') }}</p>
            @endif
            @yield('content')
        </main>

        <footer class="bg-gradient-to-b from-transparent to-sky-100/70">
            <div class="mx-auto flex w-full max-w-5xl flex-col gap-3 px-5 py-6 text-sm text-stone-500 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-stone-700">{{ config('company.legal_name') }} · IDNO {{ config('company.idno') }}</p>
                    <p class="mt-1">{{ config('company.address') }}</p>
                    <p class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1">
                        <a href="tel:{{ config('company.phone') }}" class="inline-flex items-center gap-1.5 hover:text-stone-900">
                            <x-site.icon name="phone" class="h-3.5 w-3.5" />
                            {{ config('company.phone_display') }}
                        </a>
                        <a href="mailto:{{ config('company.email') }}" class="inline-flex items-center gap-1.5 hover:text-stone-900">
                            <x-site.icon name="mail" class="h-3.5 w-3.5" />
                            {{ config('company.email') }}
                        </a>
                    </p>
                    <p class="mt-3 flex flex-wrap gap-x-4 gap-y-1">
                        <a href="{{ route('privacy') }}" class="hover:text-stone-900">{{ __('ui.privacy') }}</a>
                        <button type="button" data-cookie-manage class="cursor-pointer hover:text-stone-900">{{ __('ui.cookie_settings') }}</button>
                    </p>
                </div>
                <p class="inline-flex items-center gap-1.5">
                    <x-site.icon name="clock" class="h-3.5 w-3.5" />
                    {{ __('ui.footer_hours', ['hours' => config('company.hours')]) }}
                </p>
            </div>
        </footer>

        @unless (request()->routeIs('minigame'))
        <div data-contact-dock class="fixed right-5 bottom-5 z-40 flex flex-col items-end gap-3">
            <section id="contact-panel" data-contact-panel hidden class="w-80 max-w-[calc(100vw-2.5rem)] rounded-3xl bg-white p-5 shadow-[0_18px_50px_rgba(28,25,23,0.16)]">
                <h2 class="text-base font-semibold tracking-tight">{{ __('ui.contact_title') }}</h2>
                <ul class="mt-4 space-y-3 text-sm">
                    <li>
                        <p class="text-xs tracking-wide text-stone-400 uppercase">{{ __('ui.order_phone') }}</p>
                        <a href="tel:{{ config('company.phone') }}" class="font-medium text-blue-800">{{ config('company.phone_display') }}</a>
                    </li>
                    <li>
                        <p class="text-xs tracking-wide text-stone-400 uppercase">Email</p>
                        <a href="mailto:{{ config('company.email') }}" class="font-medium text-stone-900">{{ config('company.email') }}</a>
                    </li>
                    <li>
                        <p class="text-xs tracking-wide text-stone-400 uppercase">{{ __('ui.address_label') }}</p>
                        <p>{{ config('company.address') }}</p>
                    </li>
                    <li>
                        <p class="text-xs tracking-wide text-stone-400 uppercase">{{ __('ui.hours_label') }}</p>
                        <p>{{ __('ui.footer_hours', ['hours' => config('company.hours')]) }}</p>
                    </li>
                </ul>
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach (config('company.socials') as $social)
                        <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" class="rounded-full bg-white/80 px-3 py-1 text-xs font-medium text-stone-700 hover:bg-sky-50 hover:text-blue-800">{{ $social['label'] }}</a>
                    @endforeach
                </div>
            </section>
            <button type="button" data-contact-toggle aria-expanded="false" aria-controls="contact-panel" class="contact-pulse grid h-14 w-14 cursor-pointer place-items-center rounded-full bg-blue-600 text-white shadow-lg shadow-blue-900/30">
                <span class="sr-only">{{ __('ui.contact_open') }}</span>
                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M6 18l-2 3 4-1.5A9 9 0 1 0 6 18z"></path>
                </svg>
            </button>
        </div>
        @endunless

        <nav aria-label="{{ __('ui.menu') }}" class="fixed inset-x-3 bottom-3 z-30 grid grid-cols-3 rounded-2xl border border-white/80 bg-white/92 p-1 shadow-[0_16px_40px_rgba(37,99,235,0.16)] backdrop-blur-xl sm:hidden">
            @foreach ($siteNav as $item)
                <a href="{{ route($item['route']) }}" @class(['flex flex-col items-center gap-0.5 rounded-xl py-2 text-[11px] font-medium', 'bg-blue-600 text-white' => $item['active'], 'text-stone-600' => ! $item['active']]) @if ($item['active']) aria-current="page" @endif>
                    <x-site.icon :name="$item['icon']" class="h-4 w-4" />
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        @include('partials.cookies')
    </body>
</html>
