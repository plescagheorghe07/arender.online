@extends('layouts.app')

@section('title', __('ui.seo_news_title'))
@section('description', __('ui.seo_news_description'))

@section('content')
    <div class="enter">
        <p class="text-sm font-medium tracking-wide text-blue-700 uppercase">{{ config('app.name') }}</p>
        <h1 class="mt-3 max-w-2xl text-4xl font-semibold tracking-tight sm:text-5xl">{{ __('ui.news_title') }}</h1>
        <p class="mt-4 max-w-2xl text-lg leading-8 text-stone-600">{{ __('ui.news_lede') }}</p>
    </div>

    @if ($articles->isEmpty())
        <p class="mt-10 text-stone-600">{{ __('ui.news_empty') }}</p>
    @else
        <div data-reveal class="mt-10 grid gap-4 lg:grid-cols-2">
            @foreach ($articles as $article)
                <a href="{{ route('news.show', $article->slug) }}" @class(['group flex h-full flex-col rounded-[1.75rem] bg-white p-6 shadow-[0_12px_32px_rgba(28,25,23,0.06)] transition hover:-translate-y-1 hover:shadow-[0_18px_40px_rgba(37,99,235,0.14)]', 'bg-gradient-to-br from-white via-white to-sky-50 lg:col-span-2 lg:p-8' => $loop->first])>
                    <time datetime="{{ $article->publishedOn }}" class="text-sm text-amber-700">{{ $article->date }}</time>
                    <h2 @class(['mt-3 font-semibold tracking-tight', 'text-3xl' => $loop->first, 'text-xl' => ! $loop->first])>{{ $article->title }}</h2>
                    <p class="mt-3 line-clamp-3 max-w-3xl text-sm leading-6 text-stone-600">{{ \Illuminate\Support\Str::limit($article->body, $loop->first ? 220 : 140) }}</p>
                    <span class="mt-5 text-sm font-medium text-blue-800">{{ __('ui.read') }}</span>
                </a>
            @endforeach
        </div>
    @endif
@endsection
