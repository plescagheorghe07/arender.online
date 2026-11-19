@extends('layouts.app')

@section('title', __('ui.nav_catalog'))
@section('description', __('ui.home_lede'))

@push('head')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => config('app.name'),
            'description' => __('ui.home_lede'),
            'url' => route('home'),
            'telephone' => config('company.phone'),
            'email' => config('company.email'),
            'image' => $products->first()?->imageUrl,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => config('company.address'),
                'addressLocality' => config('company.city'),
                'addressCountry' => 'MD',
            ],
            'openingHours' => 'Mo-Su '.config('company.hours'),
            'currenciesAccepted' => 'MDL',
            'paymentAccepted' => 'Cash, Bank Transfer',
            'areaServed' => config('company.city'),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
@endpush

@section('content')
    <section class="relative overflow-hidden rounded-[2rem] bg-sky-50 shadow-[0_18px_50px_rgba(37,99,235,0.14)]" style="min-height:26rem">
        <img src="{{ asset('images/home-gamers.jpg') }}" alt="{{ __('ui.home_image') }}" class="hero-photo absolute top-0 right-0 bottom-0 left-0 h-full w-full" style="object-fit:cover;object-position:right center">
        <div class="pointer-events-none absolute top-8 right-8 h-40 w-40 rounded-full bg-cyan-300/70 blur-3xl"></div>
        <div class="pointer-events-none absolute right-28 bottom-4 h-32 w-32 rounded-full bg-violet-300/70 blur-3xl"></div>
        <div class="hero-wash absolute top-0 right-0 bottom-0 left-0"></div>
        <div class="enter relative max-w-xl px-6 py-10 sm:px-12 sm:py-14">
            <p class="text-sm font-medium tracking-wide text-blue-800 uppercase">{{ __('ui.since', ['year' => config('company.since')]) }}</p>
            <h1 class="web3-title mt-4 text-4xl font-semibold tracking-tight text-balance sm:text-6xl">{{ __('ui.home_title') }}</h1>
            <p class="mt-5 max-w-xl text-lg leading-8 text-stone-600">{{ __('ui.home_lede') }}</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-2 rounded-full bg-blue-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm shadow-blue-800/20 hover:bg-blue-700">
                    <x-site.icon name="grid" class="h-4 w-4" />
                    {{ __('ui.view_all') }}
                </a>
                <a href="tel:{{ config('company.phone') }}" class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-2.5 text-sm font-medium text-stone-800 shadow-sm hover:text-blue-700">
                    <x-site.icon name="phone" class="h-4 w-4" />
                    {{ __('ui.call') }} · {{ config('company.phone_display') }}
                </a>
            </div>
        </div>
    </section>

    <div data-reveal class="mt-6 grid gap-4 sm:grid-cols-3">
        @foreach (__('ui.promises') as $promise)
            @php
                $promiseMeta = [
                    ['card' => 'bg-sky-100/80', 'chip' => 'bg-white text-blue-600', 'icon' => 'bolt'],
                    ['card' => 'bg-cyan-50', 'chip' => 'bg-white text-cyan-700', 'icon' => 'truck'],
                    ['card' => 'bg-violet-50', 'chip' => 'bg-white text-violet-700', 'icon' => 'shield'],
                ];
                $meta = $promiseMeta[$loop->index] ?? $promiseMeta[0];
            @endphp
            <article @class(['rounded-[1.75rem] p-5 shadow-[0_10px_28px_rgba(28,25,23,0.04)]', $meta['card']])>
                <span @class(['grid h-10 w-10 place-items-center rounded-2xl shadow-sm', $meta['chip']])>
                    <x-site.icon :name="$meta['icon']" class="h-5 w-5" />
                </span>
                <h2 class="mt-4 text-lg font-semibold tracking-tight text-stone-950">{{ $promise['title'] }}</h2>
                <p class="mt-2 text-sm leading-6 text-stone-600">{{ $promise['text'] }}</p>
            </article>
        @endforeach
    </div>

    <div data-reveal class="mt-16">
    <div class="flex flex-wrap items-end justify-between gap-x-6 gap-y-2">
        <div>
            <p class="text-sm font-medium tracking-wide text-stone-500 uppercase">{{ __('ui.featured') }}</p>
            <h2 class="mt-2 text-3xl font-semibold tracking-tight">{{ __('ui.catalog_title') }}</h2>
        </div>
        <a href="{{ route('catalog.index') }}" class="text-sm font-medium text-blue-800 hover:text-blue-950">{{ __('ui.view_all') }}</a>
    </div>

    @if ($products->isEmpty())
        <p class="mt-6 text-stone-600">{{ __('ui.empty') }}</p>
    @else
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($products as $product)
                @include('catalog.card', ['product' => $product])
            @endforeach
        </div>
    @endif
    </div>

    <section data-reveal class="mt-16">
        <h2 class="text-3xl font-semibold tracking-tight">{{ __('ui.steps_title') }}</h2>
        <ol class="mt-6 grid gap-4 sm:grid-cols-3">
            @foreach (__('ui.steps') as $step)
                @php
                    $stepMeta = [
                        ['chip' => 'bg-sky-100 text-blue-700', 'icon' => 'grid'],
                        ['chip' => 'bg-cyan-100 text-cyan-800', 'icon' => 'pen'],
                        ['chip' => 'bg-violet-100 text-violet-800', 'icon' => 'package'],
                    ];
                    $meta = $stepMeta[$loop->index] ?? $stepMeta[0];
                @endphp
                <li class="rounded-[1.75rem] bg-white/85 p-6 shadow-[0_12px_32px_rgba(28,25,23,0.05)] backdrop-blur-sm">
                    <span @class(['grid h-10 w-10 place-items-center rounded-2xl', $meta['chip']])>
                        <x-site.icon :name="$meta['icon']" class="h-5 w-5" />
                    </span>
                    <h3 class="mt-4 text-lg font-semibold tracking-tight">{{ $step['title'] }}</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-600">{{ $step['text'] }}</p>
                </li>
            @endforeach
        </ol>
    </section>

    @if ($articles->isNotEmpty())
        <section data-reveal class="mt-16">
            <div class="flex items-end justify-between gap-4">
                <h2 class="text-3xl font-semibold tracking-tight">{{ __('ui.news_home') }}</h2>
                <a href="{{ route('news.index') }}" class="text-sm font-medium text-blue-800">{{ __('ui.nav_news') }}</a>
            </div>
            <div class="mt-6 grid gap-4 lg:grid-cols-3">
                @foreach ($articles as $article)
                    <a href="{{ route('news.show', $article->slug) }}" class="flex h-full flex-col rounded-[1.75rem] bg-white p-6 shadow-[0_12px_32px_rgba(28,25,23,0.06)] transition hover:-translate-y-1 hover:shadow-[0_18px_40px_rgba(6,78,59,0.12)]">
                        <time datetime="{{ $article->publishedOn }}" class="text-sm text-amber-700">{{ $article->date }}</time>
                        <h3 class="mt-3 text-lg font-semibold tracking-tight">{{ $article->title }}</h3>
                        <p class="mt-3 line-clamp-3 flex-1 text-sm leading-6 text-stone-600">{{ \Illuminate\Support\Str::limit($article->body, 140) }}</p>
                        <span class="mt-4 text-sm font-medium text-blue-800">{{ __('ui.read') }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
@endsection
