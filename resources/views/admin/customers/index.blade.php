@extends('layouts.admin')

@section('title', __('admin.customers'))

@section('content')
    <x-admin.data-table :is-empty="$customers->isEmpty()" :empty="$query === '' ? __('admin.empty') : __('admin.no_matches')">
        <x-slot:toolbar>
            <h1 class="mr-2 text-base font-semibold">{{ __('admin.customers') }}</h1>
            <x-admin.search class="ml-auto w-full max-w-sm" :action="route('admin.customers.index')" :value="$query" :placeholder="__('admin.customer_search')" />
            <x-admin.button :href="route('admin.customers.create')">{{ __('admin.new_customer') }}</x-admin.button>
        </x-slot:toolbar>
        <x-slot:head>
            <th class="px-4 py-3 font-medium">{{ __('admin.name') }}</th>
            <th class="px-4 py-3 font-medium">{{ __('admin.phones') }}</th>
            <th class="px-4 py-3 font-medium">{{ __('admin.emails') }}</th>
            <th class="px-4 py-3"></th>
        </x-slot:head>
        @foreach ($customers as $customer)
            <tr class="hover:bg-stone-50">
                <td class="px-4 py-3 font-medium">{{ $customer->name }}</td>
                <td class="px-4 py-3 text-stone-600">{{ $customer->phones->pluck('phone')->join(' · ') }}</td>
                <td class="px-4 py-3 text-stone-500">{{ $customer->emails->pluck('email')->join(' · ') }}</td>
                <td class="px-2 py-2">
                    <x-admin.row-menu :edit="route('admin.customers.edit', $customer)" :delete="route('admin.customers.destroy', $customer)" />
                </td>
            </tr>
        @endforeach
    </x-admin.data-table>
@endsection
