@extends('layouts.app')

@section('title', $product->name.' · '.__('ui.nav_catalog').' '.config('company.city'))
@section('description', ($product->description !== '' ? $product->description : $product->details).' '.__('ui.seo_catalog_description'))
@section('og_image', $product->imageUrl ?: asset('images/home-gamers.jpg'))

@push('head')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => $product->description !== '' ? $product->description : $product->details,
            'image' => $product->imageUrl,
            'brand' => ['@type' => 'Brand', 'name' => 'Arender'],
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => 'MDL',
                'price' => collect($product->prices)->min('price'),
                'availability' => 'https://schema.org/InStock',
                'url' => url()->current(),
                'areaServed' => config('company.city'),
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
@endpush

@section('content')
    <a href="{{ route('catalog.index') }}" class="text-sm font-medium text-blue-800 hover:text-blue-950">{{ __('ui.back') }}</a>

    <div class="mt-6 grid items-start gap-8 lg:grid-cols-[minmax(0,1.15fr)_minmax(0,0.85fr)]">
        <div data-reveal>
            <div data-gallery class="rounded-[1.75rem] bg-gradient-to-br from-sky-100 via-white to-violet-100 p-3 shadow-[0_16px_40px_rgba(37,99,235,0.1)]">
                <div class="flex aspect-[4/3] items-center justify-center overflow-hidden rounded-[1.25rem]">
                    @if ($product->imageUrl)
                        <img data-gallery-main src="{{ $product->imageUrl }}" alt="{{ $product->name }}" class="h-full w-full object-contain p-6 transition duration-300">
                    @endif
                </div>
                @if (count($product->imageUrls) > 1)
                    <div class="mt-3 flex gap-2 overflow-x-auto">
                        @foreach ($product->imageUrls as $imageUrl)
                            <button type="button" data-gallery-thumb="{{ $imageUrl }}" class="h-16 w-16 shrink-0 cursor-pointer overflow-hidden rounded-2xl bg-white shadow-sm">
                                <img src="{{ $imageUrl }}" alt="" class="h-full w-full object-contain p-1">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <p class="mt-8 text-sm font-medium tracking-wide text-blue-700 uppercase">{{ __('ui.details') }}</p>
            <h1 class="mt-2 text-4xl font-semibold tracking-tight text-balance sm:text-5xl">{{ $product->name }}</h1>
            @if ($product->description !== '')
                <p class="mt-4 max-w-2xl text-lg leading-8 text-stone-600">{{ $product->description }}</p>
            @endif
            @if ($product->details !== '')
                <p class="mt-6 max-w-2xl rounded-[1.5rem] bg-white px-5 py-4 leading-7 whitespace-pre-line text-stone-700 shadow-[0_12px_32px_rgba(28,25,23,0.05)]">{{ $product->details }}</p>
            @endif

            @if ($product->prices !== [])
                <div class="mt-8 grid gap-2">
                    @foreach ($product->prices as $price)
                        <div @class(['flex items-center justify-between gap-4 rounded-2xl px-4 py-3 shadow-[0_8px_24px_rgba(37,99,235,0.06)]', 'bg-sky-100' => $price->isBest, 'bg-white/80' => ! $price->isBest])>
                            <span class="text-sm text-stone-700">
                                {{ __('ui.for_days', ['days' => $price->days]) }}
                                @if ($price->isBest)
                                    <span class="ml-2 rounded-full bg-blue-700 px-2 py-0.5 text-xs font-medium text-white">{{ __('ui.best') }}</span>
                                @endif
                            </span>
                            <span class="text-sm font-semibold">{{ number_format($price->price, 0, ',', ' ') }} MDL</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <form data-reveal data-order-form method="POST" action="{{ route('catalog.orders.store', $product->slug) }}" class="reveal-late overflow-hidden rounded-[1.75rem] bg-white shadow-[0_18px_50px_rgba(6,78,59,0.12)] lg:sticky lg:top-24">
            @csrf
            <div class="bg-gradient-to-r from-blue-700 via-blue-600 to-violet-500 px-6 py-5 text-white">
                <h2 class="text-lg font-semibold tracking-tight">{{ __('ui.order_title') }}</h2>
                <p class="mt-1 text-sm leading-6 text-sky-50">{{ __('ui.order_hint') }}</p>
            </div>
            <div class="p-6">
                <label class="block text-sm font-medium" for="name">{{ __('ui.order_name') }}</label>
                <input id="name" name="name" value="{{ old('name') }}" required autocomplete="name" class="site-input">
                @error('name') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror

                <label class="mt-4 block text-sm font-medium" for="phone">{{ __('ui.order_phone') }}</label>
                <input id="phone" name="phone" type="tel" autocomplete="tel" value="{{ old('phone') }}" required class="site-input">
                @error('phone') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror

                <label class="mt-4 block text-sm font-medium" for="email">{{ __('ui.order_email') }}</label>
                <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required class="site-input">
                @error('email') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror

                <label class="mt-4 block text-sm font-medium" for="address">{{ __('ui.order_address') }}</label>
                <input id="address" name="address" autocomplete="street-address" value="{{ old('address') }}" required class="site-input">
                @error('address') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror

                <label class="mt-4 block text-sm font-medium" for="days">{{ __('ui.order_days') }}</label>
                <select id="days" name="days" data-order-days required class="site-input">
                    @foreach ($product->prices as $price)
                        <option value="{{ $price->days }}" data-price="{{ $price->price }}" @selected(old('days') === $price->days)>
                            {{ __('ui.for_days', ['days' => $price->days]) }} · {{ number_format($price->price, 0, ',', ' ') }} MDL
                        </option>
                    @endforeach
                </select>
                @error('days') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror

                @if ($product->addons !== [])
                    <fieldset class="mt-4">
                        <legend class="text-sm font-medium">{{ __('ui.order_addons') }}</legend>
                        <div class="mt-2 space-y-2">
                            @foreach ($product->addons as $addon)
                                <label class="flex items-center justify-between gap-3 rounded-2xl bg-[#f4f1ea] px-3 py-2.5 text-sm">
                                    <span class="flex items-center gap-2">
                                        <input type="checkbox" name="addons[]" value="{{ $addon->id }}" data-order-addon data-price="{{ $addon->price }}" class="accent-blue-700" @checked(in_array($addon->id, array_map('intval', old('addons', [])), true))>
                                        {{ $addon->title }}
                                    </span>
                                    <span class="text-stone-500">+{{ number_format($addon->price, 0, ',', ' ') }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                @endif

                <label class="mt-4 block text-sm font-medium" for="note">{{ __('ui.order_note') }}</label>
                <textarea id="note" name="note" rows="3" class="site-input">{{ old('note') }}</textarea>

                <label class="mt-4 block text-sm font-medium" for="promo_code">{{ __('ui.order_promo') }}</label>
                <input id="promo_code" name="promo_code" value="{{ old('promo_code') }}" class="site-input">

                <div class="mt-5 rounded-2xl bg-sky-50 px-3 py-3">
                    <label class="flex items-start gap-3 text-sm leading-6 text-stone-700">
                        <input type="checkbox" name="privacy" value="1" required class="mt-1 accent-blue-700" @checked(old('privacy'))>
                        <span>
                            {{ __('ui.order_privacy_before') }}
                            <a href="{{ route('privacy') }}" class="font-medium text-blue-800 underline decoration-blue-700/30 underline-offset-2">{{ __('ui.order_privacy_link') }}</a>
                            {{ __('ui.order_privacy_after', ['legal' => config('company.legal_name')]) }}
                        </span>
                    </label>
                    @error('privacy') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div class="mt-5 flex items-end justify-between gap-4 rounded-2xl bg-gradient-to-r from-sky-50 via-white to-violet-50 px-4 py-3">
                    <span class="inline-flex items-center gap-1.5 text-sm text-stone-600">
                        <x-site.icon name="spark" class="h-4 w-4 text-blue-600" />
                        {{ __('ui.order_total') }}
                    </span>
                    <span data-order-total class="text-2xl font-semibold tracking-tight text-blue-950">0 MDL</span>
                </div>

                <button type="submit" class="mt-4 inline-flex cursor-pointer rounded-full bg-blue-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm shadow-blue-800/20 hover:bg-blue-700">{{ __('ui.order_submit') }}</button>
            </div>
        </form>
    </div>
@endsection
