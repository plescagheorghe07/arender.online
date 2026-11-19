@extends('layouts.app')

@section('title', __('privacy.title'))
@section('description', __('privacy.lede'))

@section('content')
    @php
        $facts = [
            ':legal' => config('company.legal_name'),
            ':idno' => config('company.idno'),
            ':address' => config('company.address'),
            ':email' => config('company.email'),
            ':phone' => config('company.phone_display'),
            ':hours' => config('company.hours'),
        ];
    @endphp

    <article data-reveal class="overflow-hidden rounded-[2rem] bg-white shadow-[0_16px_40px_rgba(28,25,23,0.06)]">
        <div class="h-1.5 bg-gradient-to-r from-blue-600 via-cyan-400 to-violet-400"></div>
        <div class="px-6 py-8 sm:px-10 sm:py-12">
            <p class="text-sm font-medium tracking-wide text-blue-700 uppercase">{{ config('company.legal_name') }}</p>
            <h1 class="mt-3 max-w-3xl text-4xl font-semibold tracking-tight text-balance">{{ __('privacy.title') }}</h1>
            <p class="mt-4 max-w-2xl text-lg leading-8 text-stone-600">{{ __('privacy.lede') }}</p>
            <div class="mt-10 max-w-3xl space-y-8">
                @foreach (__('privacy.sections') as $section)
                    <section>
                        <h2 class="text-xl font-semibold tracking-tight">{{ $section['title'] }}</h2>
                        @foreach ($section['paragraphs'] as $paragraph)
                            <p class="mt-3 leading-7 text-stone-700">{{ strtr($paragraph, $facts) }}</p>
                        @endforeach
                    </section>
                @endforeach
            </div>
        </div>
    </article>
@endsection
