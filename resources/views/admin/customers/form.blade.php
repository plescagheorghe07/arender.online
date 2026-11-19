@extends('layouts.admin')

@section('title', $customer->exists ? $customer->name : __('admin.new_customer'))

@section('crumb', $customer->exists ? $customer->name : __('admin.new_customer'))

@section('content')
    <x-admin.page-header :title="$customer->exists ? $customer->name : __('admin.new_customer')">
        <x-slot:actions>
            <x-admin.button form="customer-form">{{ __('admin.save') }}</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.sheet class="mt-4" initial="contact">
        @if ($customer->exists)
            <div data-record-bar class="admin-tabbar flex gap-1 overflow-x-auto px-3">
                <x-admin.sheet-tab name="contact" active>{{ __('admin.tab_contact') }}</x-admin.sheet-tab>
                <x-admin.sheet-tab name="orders">{{ __('admin.recent_orders') }}</x-admin.sheet-tab>
            </div>
        @endif

        <div data-record-panel="contact" class="p-4">
            <form id="customer-form" method="POST" action="{{ $customer->exists ? route('admin.customers.update', $customer) : route('admin.customers.store') }}">
                @csrf
                @if ($customer->exists)
                    @method('PUT')
                @endif

                <x-admin.field name="name" :label="__('admin.name')" :value="old('name', $customer->name)" required />

                <div class="mt-5">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-semibold">{{ __('admin.phones') }}</h2>
                        <button type="button" data-add-row="#phone-template" data-target="#phone-rows" class="cursor-pointer text-sm font-medium text-emerald-900">{{ __('admin.add_phone') }}</button>
                    </div>
                    <div id="phone-rows" class="mt-2 space-y-2">
                        @foreach ($phones as $index => $value)
                            @include('admin.customers.value-row', ['name' => 'phones', 'index' => $index, 'value' => $value, 'type' => 'text'])
                        @endforeach
                    </div>
                    @error('phones') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div class="mt-5">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-semibold">{{ __('admin.emails') }}</h2>
                        <button type="button" data-add-row="#email-template" data-target="#email-rows" class="cursor-pointer text-sm font-medium text-emerald-900">{{ __('admin.add_email') }}</button>
                    </div>
                    <div id="email-rows" class="mt-2 space-y-2">
                        @foreach ($emails as $index => $value)
                            @include('admin.customers.value-row', ['name' => 'emails', 'index' => $index, 'value' => $value, 'type' => 'email'])
                        @endforeach
                    </div>
                    @error('emails.*') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div class="mt-5">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-semibold">{{ __('admin.addresses') }}</h2>
                        <button type="button" data-add-row="#address-template" data-target="#address-rows" class="cursor-pointer text-sm font-medium text-emerald-900">{{ __('admin.add_address') }}</button>
                    </div>
                    <div id="address-rows" class="mt-2 space-y-2">
                        @foreach ($addresses as $index => $value)
                            @include('admin.customers.value-row', ['name' => 'addresses', 'index' => $index, 'value' => $value, 'type' => 'text'])
                        @endforeach
                    </div>
                </div>
            </form>

            @if ($customer->exists)
                <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}" class="mt-6" data-confirm="{{ __('admin.delete_confirm') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="cursor-pointer text-sm text-red-700">{{ __('admin.delete') }}</button>
                </form>
            @endif
        </div>

        @if ($customer->exists)
            <div data-record-panel="orders" class="hidden">
                <x-admin.data-table plain :is-empty="$recentOrders->isEmpty()" :empty="__('admin.empty')">
                    <x-slot:head>
                        <th class="px-4 py-3 font-medium">{{ __('admin.products') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.total') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.status') }}</th>
                        <th class="px-4 py-3"></th>
                    </x-slot:head>
                    @foreach ($recentOrders as $order)
                        <tr class="hover:bg-stone-50">
                            <td class="px-4 py-3">
                                <p class="font-medium">{{ $order->product_name }}</p>
                                <p class="text-stone-500">{{ $order->created_at?->timezone(config('app.timezone'))->isoFormat('LL') }}</p>
                            </td>
                            <td class="px-4 py-3">{{ number_format($order->price, 0, ',', ' ') }} MDL</td>
                            <td class="px-4 py-3">@include('admin.orders.status', ['status' => $order->status])</td>
                            <td class="px-2 py-2"><x-admin.row-menu :open="route('admin.orders.show', $order)" /></td>
                        </tr>
                    @endforeach
                </x-admin.data-table>
            </div>
        @endif
    </x-admin.sheet>

    <template id="phone-template">
        @include('admin.customers.value-row', ['name' => 'phones', 'index' => '__INDEX__', 'value' => '', 'type' => 'text'])
    </template>
    <template id="email-template">
        @include('admin.customers.value-row', ['name' => 'emails', 'index' => '__INDEX__', 'value' => '', 'type' => 'email'])
    </template>
    <template id="address-template">
        @include('admin.customers.value-row', ['name' => 'addresses', 'index' => '__INDEX__', 'value' => '', 'type' => 'text'])
    </template>
@endsection
