@extends('layouts.admin')

@section('title', __('admin.dashboard'))

@section('content')
    <x-admin.page-header :title="__('admin.dashboard')" />

    <div @class(['mt-5 grid gap-3', 'sm:grid-cols-3' => auth()->user()->managesCatalog()])>
        @if (auth()->user()->managesCatalog())
            <x-admin.stat :href="route('admin.products.index')" :label="__('admin.products_count')" :value="$products" />
            <x-admin.stat :href="route('admin.news.index')" :label="__('admin.articles_count')" :value="$articles" />
        @endif
        <x-admin.stat :href="route('admin.orders.index', ['status' => 'new'])" :label="__('admin.new_orders')" :value="$newOrders" tone="emerald" />
    </div>

    <x-admin.data-table class="mt-4" :is-empty="$orders->isEmpty()">
        <x-slot:toolbar>
            <h2 class="mr-auto text-sm font-medium text-stone-700">{{ __('admin.latest_orders') }}</h2>
            <x-admin.button :href="route('admin.orders.index')" variant="ghost">{{ __('admin.all_orders') }}</x-admin.button>
        </x-slot:toolbar>
        <x-slot:head>
            <th class="px-4 py-3 font-medium">{{ __('admin.name') }}</th>
            <th class="px-4 py-3 font-medium">{{ __('admin.products') }}</th>
            <th class="px-4 py-3 font-medium">{{ __('admin.total') }}</th>
            <th class="px-4 py-3 font-medium">{{ __('admin.status') }}</th>
            <th class="px-4 py-3"></th>
        </x-slot:head>
        @foreach ($orders as $order)
            <tr class="hover:bg-stone-50">
                <td class="px-4 py-3 font-medium">{{ $order->customer_name }}</td>
                <td class="px-4 py-3 text-stone-600">{{ $order->product_name }}</td>
                <td class="px-4 py-3">{{ number_format($order->price, 0, ',', ' ') }} MDL</td>
                <td class="px-4 py-3">@include('admin.orders.status', ['status' => $order->status])</td>
                <td class="px-2 py-2"><x-admin.row-menu :open="route('admin.orders.show', $order)" /></td>
            </tr>
        @endforeach
    </x-admin.data-table>
@endsection
