@php
    $productQuery = trim((string) request('q', ''));
    $productState = (string) request('state', '');
@endphp

@extends('layouts.admin')

@section('title', __('admin.products'))

@section('content')
    <x-admin.data-table data-reorder="{{ route('admin.products.reorder') }}" :is-empty="$products->isEmpty()">
        <x-slot:toolbar>
            <h1 class="mr-2 text-base font-semibold">{{ __('admin.products') }}</h1>
            <label class="flex cursor-pointer items-center gap-2 rounded-full bg-[#ebe7e2] px-3 py-1.5 text-sm font-medium">
                <input type="checkbox" data-reorder-toggle class="accent-emerald-900">
                {{ __('admin.reorder') }}
            </label>
            <x-admin.segments>
                <x-admin.filter :href="route('admin.products.index', request()->except('state'))" :active="$productState === ''">{{ __('admin.all_orders') }}</x-admin.filter>
                <x-admin.filter :href="route('admin.products.index', ['state' => 'active', 'q' => $productQuery])" :active="$productState === 'active'">{{ __('admin.active') }}</x-admin.filter>
                <x-admin.filter :href="route('admin.products.index', ['state' => 'inactive', 'q' => $productQuery])" :active="$productState === 'inactive'">{{ __('admin.inactive') }}</x-admin.filter>
            </x-admin.segments>
            <x-admin.search class="ml-auto w-full max-w-sm" :action="route('admin.products.index')" :value="$productQuery" />
            <x-admin.button :href="route('admin.products.create')">{{ __('admin.new_product') }}</x-admin.button>
        </x-slot:toolbar>
        <x-slot:head>
            <th class="reorder-handle hidden w-8"></th>
            <th class="px-4 py-3 font-medium">{{ __('admin.products') }}</th>
            <th class="px-4 py-3 font-medium">{{ __('admin.status') }}</th>
            <th class="px-4 py-3"></th>
        </x-slot:head>
        @foreach ($products as $product)
            @php
                $matchesState = $productState === ''
                    || ($productState === 'active' && $product->is_active)
                    || ($productState === 'inactive' && ! $product->is_active);
                $matchesQuery = $productQuery === '' || str_contains(mb_strtolower($product->name), mb_strtolower($productQuery));
                $matches = $matchesState && $matchesQuery;
                $cover = $product->images->first()?->path ?? $product->image;
            @endphp
            <tr data-id="{{ $product->id }}" data-match="{{ $matches ? '1' : '0' }}" @class(['hover:bg-stone-50', 'hidden' => ! $matches])>
                <td class="reorder-handle hidden px-2 text-stone-400"><x-admin.icon name="grip" class="h-4 w-4" /></td>
                <td class="px-4 py-3">
                    <span class="flex items-center gap-3">
                        @if ($cover)
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($cover) }}" alt="" class="h-11 w-11 rounded-xl bg-stone-100 object-contain">
                        @endif
                        <span class="font-medium">{{ $product->name }}</span>
                    </span>
                </td>
                <td class="px-4 py-3 text-stone-600">{{ $product->is_active ? __('admin.active') : __('admin.inactive') }}</td>
                <td class="px-2 py-2">
                    <x-admin.row-menu :edit="route('admin.products.edit', $product)" :delete="route('admin.products.destroy', $product)" />
                </td>
            </tr>
        @endforeach
    </x-admin.data-table>
@endsection
