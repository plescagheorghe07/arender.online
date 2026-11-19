@php
    $recordTab = 'details';

    foreach ($errors->keys() as $key) {
        if (str_starts_with($key, 'images') || str_starts_with($key, 'keep_images')) {
            $recordTab = 'photos';
        }
    }
@endphp

@extends('layouts.admin')

@section('title', $article->exists ? $article->title_ro : __('admin.new_article'))

@section('crumb', $article->exists ? $article->title_ro : __('admin.new_article'))

@section('content')
    <x-admin.page-header :title="$article->exists ? $article->title_ro : __('admin.new_article')">
        <x-slot:actions>
            <x-admin.button form="news-form">{{ __('admin.save') }}</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <form id="news-form" method="POST" action="{{ $article->exists ? route('admin.news.update', $article) : route('admin.news.store') }}" enctype="multipart/form-data" class="mt-4">
        @csrf
        @if ($article->exists)
            @method('PUT')
        @endif

        <x-admin.errors />

        <x-admin.sheet class="mt-4" :initial="$recordTab">
            <div data-record-bar class="admin-tabbar flex gap-1 overflow-x-auto px-3">
                <x-admin.sheet-tab name="details" :active="$recordTab === 'details'">{{ __('admin.tab_details') }}</x-admin.sheet-tab>
                <x-admin.sheet-tab name="photos" :active="$recordTab === 'photos'">{{ __('admin.images') }}</x-admin.sheet-tab>
            </div>

            <div data-record-panel="details" @class(['p-4', 'hidden' => $recordTab !== 'details'])>
                <div class="grid gap-3 sm:grid-cols-2">
                    <x-admin.field name="slug" :label="__('admin.slug')" :value="old('slug', $article->slug)" />
                    <x-admin.field name="published_on" type="date" :label="__('admin.date')" :value="old('published_on', optional($article->published_on)->toDateString())" required />
                    <label class="flex items-center gap-2 text-sm font-medium sm:col-span-2">
                        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $article->is_published))>
                        {{ __('admin.published') }}
                    </label>
                </div>
                <x-admin.locale-tabs class="mt-4" :initial="$errors->hasAny(['title_ru', 'body_ru']) ? 'ru' : 'ro'">
                    <x-slot:ro>
                        <div class="grid gap-3">
                            <x-admin.field name="title_ro" :label="__('admin.title_ro')" :value="old('title_ro', $article->title_ro)" required />
                            <x-admin.field name="body_ro" type="textarea" :label="__('admin.body_ro')" :value="old('body_ro', $article->body_ro)" rows="8" required />
                        </div>
                    </x-slot:ro>
                    <x-slot:ru>
                        <div class="grid gap-3">
                            <x-admin.field name="title_ru" :label="__('admin.title_ru')" :value="old('title_ru', $article->title_ru)" required />
                            <x-admin.field name="body_ru" type="textarea" :label="__('admin.body_ru')" :value="old('body_ru', $article->body_ru)" rows="8" required />
                        </div>
                    </x-slot:ru>
                </x-admin.locale-tabs>
            </div>

            <div data-record-panel="photos" @class(['p-4', 'hidden' => $recordTab !== 'photos'])>
                <x-admin.attachments :images="$article->exists ? $article->images : []" />
            </div>
        </x-admin.sheet>
    </form>

    @if ($article->exists)
        <form method="POST" action="{{ route('admin.news.destroy', $article) }}" class="mt-4" data-confirm="{{ __('admin.delete_confirm') }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="cursor-pointer text-sm text-red-700">{{ __('admin.delete') }}</button>
        </form>
    @endif
@endsection
