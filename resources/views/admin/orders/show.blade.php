@extends('layouts.admin')

@section('title', $order->customer_name)

@section('crumb', $order->customer_name)

@section('content')
    @php
        $orderTab = $errors->hasAny(['customer_name', 'phone', 'email', 'address', 'note']) && ! old('from_order') ? 'details' : 'summary';
        $sideTab = $errors->has('body') ? 'notes' : 'customer';
    @endphp

    <x-admin.page-header :title="$order->customer_name">
        <x-slot:meta>{{ $order->created_at?->timezone(config('app.timezone'))->isoFormat('LLL') }}</x-slot:meta>
        <x-slot:actions>
            @include('admin.orders.status', ['status' => $order->status])
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.statusbar class="mt-4" :action="route('admin.orders.update', $order)" :current="$order->status" />

    <div class="mt-4 grid items-start gap-4 lg:grid-cols-[minmax(0,1.2fr)_minmax(18rem,0.8fr)]">
        <x-admin.sheet :initial="$orderTab">
            <div data-record-bar class="admin-tabbar flex gap-1 overflow-x-auto px-3">
                <x-admin.sheet-tab name="summary" :active="$orderTab === 'summary'">{{ __('admin.tab_order') }}</x-admin.sheet-tab>
                <x-admin.sheet-tab name="details" :active="$orderTab === 'details'">{{ __('admin.tab_details') }}</x-admin.sheet-tab>
            </div>
            <div data-record-panel="summary" @class(['p-4', 'hidden' => $orderTab !== 'summary'])>
                <h2 class="text-sm font-semibold">{{ $order->product_name }}</h2>
                <dl class="mt-3 grid grid-cols-[7.5rem_minmax(0,1fr)] gap-x-4 gap-y-2 text-sm">
                    <dt class="text-stone-500">{{ __('admin.phone') }}</dt><dd><a href="tel:{{ $order->phone }}" class="font-medium">{{ $order->phone }}</a></dd>
                    <dt class="text-stone-500">{{ __('admin.email') }}</dt><dd class="break-all">{{ $order->email }}</dd>
                    <dt class="text-stone-500">{{ __('admin.address') }}</dt><dd>{{ $order->address }}</dd>
                    <dt class="text-stone-500">{{ __('admin.days') }}</dt><dd>{{ $order->days_label }}</dd>
                    <dt class="text-stone-500">{{ __('admin.total') }}</dt><dd class="font-medium">{{ number_format($order->price, 0, ',', ' ') }} MDL</dd>
                    @if ($order->promo_code)
                        <dt class="text-stone-500">{{ __('admin.code') }}</dt><dd>{{ $order->promo_code }}</dd>
                    @endif
                    @if ($order->privacy_accepted_at)
                        <dt class="text-stone-500">{{ __('admin.privacy_at') }}</dt><dd>{{ $order->privacy_accepted_at->timezone(config('app.timezone'))->isoFormat('LLL') }}</dd>
                    @endif
                </dl>
                @if ($order->addons)
                    <ul class="mt-4 space-y-1 text-sm text-stone-600">
                        @foreach ($order->addons as $addon)
                            <li>{{ $addon['title'] ?? '' }} · {{ number_format($addon['price'] ?? 0, 0, ',', ' ') }} MDL</li>
                        @endforeach
                    </ul>
                @endif
                @if ($order->note)
                    <p class="mt-4 rounded-xl bg-stone-50 px-4 py-3 text-sm text-stone-700">{{ __('admin.customer_note') }}: {{ $order->note }}</p>
                @endif
            </div>
            <div data-record-panel="details" @class(['p-4', 'hidden' => $orderTab !== 'details'])>
                <form method="POST" action="{{ route('admin.orders.details', $order) }}" class="grid gap-3 sm:grid-cols-2">
                    @csrf
                    @method('PATCH')
                    <x-admin.field name="customer_name" :label="__('admin.name')" :value="old('customer_name', $order->customer_name)" required />
                    <x-admin.field name="phone" :label="__('admin.phone')" :value="old('phone', $order->phone)" required />
                    <x-admin.field name="email" type="email" :label="__('admin.email')" :value="old('email', $order->email)" required />
                    <x-admin.field name="address" :label="__('admin.address')" :value="old('address', $order->address)" required />
                    <x-admin.field class="sm:col-span-2" name="note" type="textarea" :label="__('admin.customer_note')" :value="old('note', $order->note)" />
                    <div><x-admin.button>{{ __('admin.save') }}</x-admin.button></div>
                </form>
            </div>
        </x-admin.sheet>

        <x-admin.sheet :initial="$sideTab">
            <div data-record-bar class="admin-tabbar flex gap-1 overflow-x-auto px-3">
                <x-admin.sheet-tab name="customer" :active="$sideTab === 'customer'">{{ __('admin.customer') }}</x-admin.sheet-tab>
                <x-admin.sheet-tab name="notes" :active="$sideTab === 'notes'">{{ __('admin.tab_notes') }}</x-admin.sheet-tab>
            </div>
            <div data-record-panel="customer" @class(['p-4', 'hidden' => $sideTab !== 'customer'])>
                @if ($order->customer)
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <a href="{{ route('admin.customers.edit', $order->customer) }}" class="font-medium text-emerald-900">{{ $order->customer->name }}</a>
                            <p class="mt-1 text-sm text-stone-700">{{ $order->customer->phones->pluck('phone')->join(' · ') }}</p>
                            @if ($order->customer->emails->isNotEmpty())
                                <p class="text-sm text-stone-700">{{ $order->customer->emails->pluck('email')->join(' · ') }}</p>
                            @endif
                            @if ($order->customer->addresses->isNotEmpty())
                                <p class="text-sm text-stone-700">{{ $order->customer->addresses->pluck('address')->join(' · ') }}</p>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('admin.orders.customer.detach', $order) }}" data-confirm="{{ __('admin.detach_confirm') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex cursor-pointer items-center gap-1.5 rounded-xl bg-[#f3f1ed] px-3 py-1.5 text-sm font-medium text-stone-700 hover:bg-red-50 hover:text-red-700">
                                <x-admin.icon name="unlink" class="h-4 w-4" />
                                {{ __('admin.detach') }}
                            </button>
                        </form>
                    </div>
                @else
                    <p class="text-sm text-stone-600">{{ __('admin.no_customer') }}</p>
                    <form method="GET" action="{{ route('admin.orders.show', $order) }}" class="mt-4 flex gap-2">
                        <input name="q" value="{{ $query }}" placeholder="{{ __('admin.customer_search') }}" class="admin-input min-w-0 flex-1">
                        <x-admin.button variant="dark">{{ __('admin.search') }}</x-admin.button>
                    </form>
                    @if ($matches->isNotEmpty())
                        <h3 class="mt-4 text-sm font-medium text-stone-500">{{ $searched ? __('admin.find_customer') : __('admin.suggestions') }}</h3>
                        <ul class="mt-3 space-y-2">
                            @foreach ($matches as $customer)
                                @include('admin.orders.customer-match', ['customer' => $customer, 'order' => $order])
                            @endforeach
                        </ul>
                    @elseif ($searched)
                        <p class="mt-4 text-sm text-stone-500">{{ __('admin.no_matches') }}</p>
                    @endif
                    @if ($searched && $matches->isEmpty() && $order->customer_id === null)
                        <x-admin.button type="button" class="mt-4" data-open-dialog="create-customer">{{ __('admin.create_from_order') }}</x-admin.button>
                    @endif
                @endif
            </div>
            <div data-record-panel="notes" @class(['p-4', 'hidden' => $sideTab !== 'notes'])>
                <p class="text-sm text-stone-500">{{ __('admin.private_hint') }}</p>
                @if ($order->notes->isEmpty())
                    <p class="mt-3 text-sm text-stone-500">{{ __('admin.no_notes') }}</p>
                @else
                    <ol class="mt-3 space-y-3">
                        @foreach ($order->notes as $note)
                            <li class="rounded-xl bg-stone-50 px-4 py-3 text-sm">
                                <p class="text-xs text-stone-500">{{ $note->user?->name ?? '—' }} · {{ $note->created_at?->timezone(config('app.timezone'))->isoFormat('LLL') }}</p>
                                <p class="mt-1 whitespace-pre-line">{{ $note->body }}</p>
                            </li>
                        @endforeach
                    </ol>
                @endif
                <form method="POST" action="{{ route('admin.orders.notes.store', $order) }}" class="mt-4">
                    @csrf
                    <x-admin.field name="body" type="textarea" :label="__('admin.add_detail')" :value="old('body')" required />
                    <x-admin.button variant="dark" class="mt-3">{{ __('admin.add_detail') }}</x-admin.button>
                </form>
            </div>
        </x-admin.sheet>
    </div>

    @if ($order->customer_id === null)
        @php
            $draftPhones = old('from_order') ? old('phones', [$order->phone]) : [$order->phone];
            $draftEmails = old('from_order') ? old('emails', [$order->email]) : array_values(array_filter([$order->email]));
            $draftAddresses = old('from_order') ? old('addresses', [$order->address]) : [$order->address];
            $draftPhones = $draftPhones === [] ? [''] : array_values($draftPhones);
            $draftEmails = $draftEmails === [] ? [''] : array_values($draftEmails);
            $draftAddresses = $draftAddresses === [] ? [''] : array_values($draftAddresses);
        @endphp
        <x-admin.dialog id="create-customer" :title="__('admin.create_from_order')" wide :open="$errors->any() && old('from_order')">
            <form method="POST" action="{{ route('admin.orders.customer.create', $order) }}" class="space-y-4">
                @csrf
                <input type="hidden" name="from_order" value="1">
                <x-admin.field name="name" :label="__('admin.name')" :value="old('from_order') ? old('name', $order->customer_name) : $order->customer_name" required />
                <div>
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium">{{ __('admin.phones') }}</p>
                        <button type="button" data-add-row="#order-phone-template" data-target="#order-phone-rows" class="cursor-pointer text-sm font-medium text-emerald-900">{{ __('admin.add_phone') }}</button>
                    </div>
                    <div id="order-phone-rows" class="mt-2 space-y-2">
                        @foreach ($draftPhones as $index => $value)
                            @include('admin.customers.value-row', ['name' => 'phones', 'index' => $index, 'value' => $value, 'type' => 'text'])
                        @endforeach
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium">{{ __('admin.emails') }}</p>
                        <button type="button" data-add-row="#order-email-template" data-target="#order-email-rows" class="cursor-pointer text-sm font-medium text-emerald-900">{{ __('admin.add_email') }}</button>
                    </div>
                    <div id="order-email-rows" class="mt-2 space-y-2">
                        @foreach ($draftEmails as $index => $value)
                            @include('admin.customers.value-row', ['name' => 'emails', 'index' => $index, 'value' => $value, 'type' => 'email'])
                        @endforeach
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium">{{ __('admin.addresses') }}</p>
                        <button type="button" data-add-row="#order-address-template" data-target="#order-address-rows" class="cursor-pointer text-sm font-medium text-emerald-900">{{ __('admin.add_address') }}</button>
                    </div>
                    <div id="order-address-rows" class="mt-2 space-y-2">
                        @foreach ($draftAddresses as $index => $value)
                            @include('admin.customers.value-row', ['name' => 'addresses', 'index' => $index, 'value' => $value, 'type' => 'text'])
                        @endforeach
                    </div>
                </div>
                <x-admin.button>{{ __('admin.create_and_attach') }}</x-admin.button>
            </form>
        </x-admin.dialog>
        <template id="order-phone-template">@include('admin.customers.value-row', ['name' => 'phones', 'index' => '__INDEX__', 'value' => '', 'type' => 'text'])</template>
        <template id="order-email-template">@include('admin.customers.value-row', ['name' => 'emails', 'index' => '__INDEX__', 'value' => '', 'type' => 'email'])</template>
        <template id="order-address-template">@include('admin.customers.value-row', ['name' => 'addresses', 'index' => '__INDEX__', 'value' => '', 'type' => 'text'])</template>
    @endif
@endsection
