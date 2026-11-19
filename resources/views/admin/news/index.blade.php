@php
    $newsQuery = trim((string) request('q', ''));
    $newsState = (string) request('state', '');
@endphp

@extends('layouts.admin')

@section('title', __('admin.news'))

@section('content')
    <x-admin.data-table data-reorder="{{ route('admin.news.reorder') }}" :is-empty="$articles->isEmpty()">
        <x-slot:toolbar>
            <h1 class="mr-2 text-base font-semibold">{{ __('admin.news') }}</h1>
            <label class="flex cursor-pointer items-center gap-2 rounded-full bg-[#ebe7e2] px-3 py-1.5 text-sm font-medium">
                <input type="checkbox" data-reorder-toggle class="accent-emerald-900">
                {{ __('admin.reorder') }}
            </label>
            <x-admin.segments>
                <x-admin.filter :href="route('admin.news.index')" :active="$newsState === '' && $newsQuery === ''">{{ __('admin.all_orders') }}</x-admin.filter>
                <x-admin.filter :href="route('admin.news.index', ['state' => 'published', 'q' => $newsQuery])" :active="$newsState === 'published'">{{ __('admin.published') }}</x-admin.filter>
                <x-admin.filter :href="route('admin.news.index', ['state' => 'draft', 'q' => $newsQuery])" :active="$newsState === 'draft'">{{ __('admin.draft') }}</x-admin.filter>
            </x-admin.segments>
            <x-admin.search class="ml-auto w-full max-w-sm" :action="route('admin.news.index')" :value="$newsQuery" />
            <x-admin.button :href="route('admin.news.create')">{{ __('admin.new_article') }}</x-admin.button>
        </x-slot:toolbar>
        <x-slot:head>
            <th class="reorder-handle hidden w-8"></th>
            <th class="px-4 py-3 font-medium">{{ __('admin.title_ro') }}</th>
            <th class="px-4 py-3 font-medium">{{ __('admin.date') }}</th>
            <th class="px-4 py-3 font-medium">{{ __('admin.status') }}</th>
            <th class="px-4 py-3"></th>
        </x-slot:head>
        @foreach ($articles as $article)
            @php
                $matchesState = $newsState === ''
                    || ($newsState === 'published' && $article->is_published)
                    || ($newsState === 'draft' && ! $article->is_published);
                $title = mb_strtolower($article->title_ro.' '.$article->title_ru);
                $matches = $matchesState && ($newsQuery === '' || str_contains($title, mb_strtolower($newsQuery)));
            @endphp
            <tr data-id="{{ $article->id }}" data-match="{{ $matches ? '1' : '0' }}" @class(['hover:bg-stone-50', 'hidden' => ! $matches])>
                <td class="reorder-handle hidden px-2 text-stone-400"><x-admin.icon name="grip" class="h-4 w-4" /></td>
                <td class="px-4 py-3 font-medium">{{ $article->title_ro }}</td>
                <td class="px-4 py-3 text-stone-500">{{ optional($article->published_on)->isoFormat('LL') }}</td>
                <td class="px-4 py-3 text-stone-600">{{ $article->is_published ? __('admin.published') : __('admin.draft') }}</td>
                <td class="px-2 py-2">
                    <x-admin.row-menu :edit="route('admin.news.edit', $article)" :delete="route('admin.news.destroy', $article)" />
                </td>
            </tr>
        @endforeach
    </x-admin.data-table>
@endsection
