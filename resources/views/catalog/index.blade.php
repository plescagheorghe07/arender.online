@extends('layouts.app')

@section('title', __('ui.catalog_title'))
@section('description', __('ui.catalog_lede'))

@section('content')
    <div class="enter">
        <p class="text-sm font-medium tracking-wide text-blue-700 uppercase">{{ config('company.city') }}</p>
        <h1 class="mt-3 text-4xl font-semibold tracking-tight sm:text-5xl">{{ __('ui.catalog_title') }}</h1>
        <p class="mt-4 max-w-2xl text-lg leading-8 text-stone-600">{{ __('ui.catalog_lede') }}</p>
    </div>

    @if ($products->isEmpty())
        <p class="mt-10 text-stone-600">{{ __('ui.empty') }}</p>
    @else
        <div data-reveal class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($products as $product)
                @include('catalog.card', ['product' => $product])
            @endforeach
        </div>
    @endif
@endsection
