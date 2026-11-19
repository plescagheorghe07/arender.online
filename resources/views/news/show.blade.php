@extends('layouts.app')

@section('title', $article->title)
@section('description', \Illuminate\Support\Str::limit($article->body, 160))
@section('og_type', 'article')

@push('head')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => $article->title,
            'datePublished' => $article->publishedOn,
            'dateModified' => $article->publishedOn,
            'articleBody' => $article->body,
            'inLanguage' => app()->getLocale(),
            'mainEntityOfPage' => url()->current(),
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
@endpush

@section('content')
    <a href="{{ route('news.index') }}" class="text-sm font-medium text-blue-800 hover:text-blue-950">{{ __('ui.back_news') }}</a>
    <article data-reveal class="mt-8 overflow-hidden rounded-[2rem] bg-white shadow-[0_16px_40px_rgba(28,25,23,0.06)]">
        <div class="h-1.5 bg-gradient-to-r from-blue-600 via-cyan-400 to-violet-400"></div>
        <div class="px-6 py-8 sm:px-10 sm:py-12">
        <time datetime="{{ $article->publishedOn }}" class="text-sm text-amber-700">{{ $article->date }}</time>
        <h1 class="mt-3 max-w-3xl text-4xl font-semibold tracking-tight text-balance">{{ $article->title }}</h1>
        @if ($article->imageUrls !== [])
            <div data-gallery class="mt-8">
                <img data-gallery-main src="{{ $article->imageUrls[0] }}" alt="" class="max-h-[28rem] w-full rounded-2xl bg-gradient-to-br from-sky-50 to-violet-50 object-contain transition duration-300">
                @if (count($article->imageUrls) > 1)
                    <div class="mt-3 flex gap-2 overflow-x-auto">
                        @foreach ($article->imageUrls as $imageUrl)
                            <button type="button" data-gallery-thumb="{{ $imageUrl }}" class="h-16 w-16 shrink-0 cursor-pointer overflow-hidden rounded-2xl bg-white shadow-sm">
                                <img src="{{ $imageUrl }}" alt="" class="h-full w-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
        <div class="mt-8 max-w-3xl text-lg leading-8 whitespace-pre-line text-stone-700">{{ $article->body }}</div>
        </div>
    </article>
@endsection
