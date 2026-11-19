@extends('layouts.admin')

@section('title', __('admin.orders'))

@section('content')
    <x-admin.data-table :is-empty="$orders->isEmpty()">
        <x-slot:toolbar>
            <h1 class="mr-2 text-base font-semibold">{{ __('admin.orders') }}</h1>
            <x-admin.segments>
                <x-admin.filter :href="route('admin.orders.index')" :active="$status === ''">{{ __('admin.all_orders') }}</x-admin.filter>
                @foreach (['new', 'confirmed', 'done', 'cancelled'] as $value)
                    <x-admin.filter :href="route('admin.orders.index', ['status' => $value])" :active="$status === $value">
                        {{ __("admin.status_$value") }}
                        <span class="text-xs opacity-70">{{ $counts[$value] ?? 0 }}</span>
                    </x-admin.filter>
                @endforeach
            </x-admin.segments>
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
                <td class="px-4 py-3">
                    <p class="font-medium">{{ $order->customer_name }}</p>
                    <p class="text-stone-500">{{ $order->phone }}@if ($order->email) · {{ $order->email }}@endif</p>
                </td>
                <td class="px-4 py-3 text-stone-600">{{ $order->product_name }} · {{ $order->days_label }}</td>
                <td class="px-4 py-3">{{ number_format($order->price, 0, ',', ' ') }} MDL</td>
                <td class="px-4 py-3">@include('admin.orders.status', ['status' => $order->status])</td>
                <td class="px-2 py-2"><x-admin.row-menu :open="route('admin.orders.show', $order)" /></td>
            </tr>
        @endforeach
    </x-admin.data-table>
@endsection
