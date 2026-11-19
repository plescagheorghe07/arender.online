@php
    $recordTab = 'details';

    foreach ($errors->keys() as $key) {
        if (str_starts_with($key, 'prices')) {
            $recordTab = 'prices';
        } elseif (str_starts_with($key, 'addons')) {
            $recordTab = 'addons';
        } elseif (str_starts_with($key, 'images') || str_starts_with($key, 'keep_images')) {
            $recordTab = 'photos';
        } elseif (str_starts_with($key, 'description') || str_starts_with($key, 'details') || in_array($key, ['name', 'slug', 'is_active'], true)) {
            $recordTab = 'details';
        }
    }
@endphp

@extends('layouts.admin')

@section('title', $product->exists ? $product->name : __('admin.new_product'))

@section('crumb', $product->exists ? $product->name : __('admin.new_product'))

@section('content')
    <x-admin.page-header :title="$product->exists ? $product->name : __('admin.new_product')">
        <x-slot:actions>
            <x-admin.button form="product-form">{{ __('admin.save') }}</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <form id="product-form" method="POST" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data" class="mt-4">
        @csrf
        @if ($product->exists)
            @method('PUT')
        @endif

        <x-admin.errors />

        <x-admin.sheet class="mt-4" :initial="$recordTab">
            <div data-record-bar class="admin-tabbar flex gap-1 overflow-x-auto px-3">
                <x-admin.sheet-tab name="details" :active="$recordTab === 'details'">{{ __('admin.tab_details') }}</x-admin.sheet-tab>
                <x-admin.sheet-tab name="prices" :active="$recordTab === 'prices'">{{ __('admin.prices') }}</x-admin.sheet-tab>
                <x-admin.sheet-tab name="addons" :active="$recordTab === 'addons'">{{ __('admin.addons') }}</x-admin.sheet-tab>
                <x-admin.sheet-tab name="photos" :active="$recordTab === 'photos'">{{ __('admin.images') }}</x-admin.sheet-tab>
            </div>

            <div data-record-panel="details" @class(['p-4', 'hidden' => $recordTab !== 'details'])>
                <div class="grid gap-3 sm:grid-cols-2">
                    <x-admin.field name="name" :label="__('admin.name')" :value="old('name', $product->name)" required />
                    <x-admin.field name="slug" :label="__('admin.slug')" :value="old('slug', $product->slug)" />
                    <label class="flex items-center gap-2 text-sm font-medium sm:col-span-2">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))>
                        {{ __('admin.active') }}
                    </label>
                </div>
                <x-admin.locale-tabs class="mt-4" :initial="$errors->hasAny(['description_ru', 'details_ru']) ? 'ru' : 'ro'">
                    <x-slot:ro>
                        <div class="grid gap-3">
                            <x-admin.field name="description_ro" type="textarea" :label="__('admin.description_ro')" :value="old('description_ro', $product->description_ro)" rows="3" />
                            <x-admin.field name="details_ro" type="textarea" :label="__('admin.details_ro')" :value="old('details_ro', $product->details_ro)" rows="3" />
                        </div>
                    </x-slot:ro>
                    <x-slot:ru>
                        <div class="grid gap-3">
                            <x-admin.field name="description_ru" type="textarea" :label="__('admin.description_ru')" :value="old('description_ru', $product->description_ru)" rows="3" />
                            <x-admin.field name="details_ru" type="textarea" :label="__('admin.details_ru')" :value="old('details_ru', $product->details_ru)" rows="3" />
                        </div>
                    </x-slot:ru>
                </x-admin.locale-tabs>
            </div>

            <div data-record-panel="prices" @class(['p-4', 'hidden' => $recordTab !== 'prices'])>
                <div class="mb-3 flex justify-end">
                    <button type="button" data-add-row="#price-template" data-target="#price-rows" class="cursor-pointer text-sm font-medium text-emerald-900">{{ __('admin.add_price') }}</button>
                </div>
                <div id="price-rows" class="space-y-2">
                    @foreach ($priceRows as $index => $row)
                        @include('admin.products.price-row', ['index' => $index, 'row' => $row])
                    @endforeach
                </div>
            </div>

            <div data-record-panel="addons" @class(['p-4', 'hidden' => $recordTab !== 'addons'])>
                <div class="mb-3 flex justify-end">
                    <button type="button" data-add-row="#addon-template" data-target="#addon-rows" class="cursor-pointer text-sm font-medium text-emerald-900">{{ __('admin.add_addon') }}</button>
                </div>
                <div id="addon-rows" class="space-y-2">
                    @foreach ($addonRows as $index => $row)
                        @include('admin.products.addon-row', ['index' => $index, 'row' => $row])
                    @endforeach
                </div>
            </div>

            <div data-record-panel="photos" @class(['p-4', 'hidden' => $recordTab !== 'photos'])>
                <x-admin.attachments :images="$product->exists ? $product->images : []" />
            </div>
        </x-admin.sheet>
    </form>

    @if ($product->exists)
        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="mt-4" data-confirm="{{ __('admin.delete_confirm') }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="cursor-pointer text-sm text-red-700">{{ __('admin.delete') }}</button>
        </form>
    @endif

    <template id="price-template">
        @include('admin.products.price-row', ['index' => '__INDEX__', 'row' => ['days' => '', 'price' => '', 'is_best' => false]])
    </template>
    <template id="addon-template">
        @include('admin.products.addon-row', ['index' => '__INDEX__', 'row' => ['title_ro' => '', 'title_ru' => '', 'price' => '']])
    </template>
@endsection
