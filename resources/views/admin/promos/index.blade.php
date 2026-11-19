@php
    $promoState = (string) request('state', '');
    $promoQuery = trim((string) request('q', ''));
    $promoRows = $codes->filter(function ($code) use ($promoState, $promoQuery): bool {
        if ($promoState === 'active' && ! $code->is_active) {
            return false;
        }

        if ($promoState === 'game' && ! $code->for_game) {
            return false;
        }

        $haystack = mb_strtolower($code->code.' '.$code->label);

        return $promoQuery === '' || str_contains($haystack, mb_strtolower($promoQuery));
    })->values();
@endphp

@extends('layouts.admin')

@section('title', __('admin.promos'))

@section('content')
    <x-admin.data-table :is-empty="$promoRows->isEmpty()">
        <x-slot:toolbar>
            <h1 class="mr-2 text-base font-semibold">{{ __('admin.promos') }}</h1>
            <x-admin.segments>
                <x-admin.filter :href="route('admin.promos.index')" :active="$promoState === '' && $promoQuery === ''">{{ __('admin.all_orders') }}</x-admin.filter>
                <x-admin.filter :href="route('admin.promos.index', ['state' => 'active', 'q' => $promoQuery])" :active="$promoState === 'active'">{{ __('admin.active') }}</x-admin.filter>
                <x-admin.filter :href="route('admin.promos.index', ['state' => 'game', 'q' => $promoQuery])" :active="$promoState === 'game'">{{ __('admin.for_game') }}</x-admin.filter>
            </x-admin.segments>
            <x-admin.search class="ml-auto w-full max-w-sm" :action="route('admin.promos.index')" :value="$promoQuery" />
            <x-admin.button type="button" data-open-dialog="promo-create">{{ __('admin.add_code') }}</x-admin.button>
        </x-slot:toolbar>
        <x-slot:head>
            <th class="px-4 py-3 font-medium">{{ __('admin.code') }}</th>
            <th class="px-4 py-3 font-medium">{{ __('admin.label') }}</th>
            <th class="px-4 py-3 font-medium">{{ __('admin.expires') }}</th>
            <th class="px-4 py-3"></th>
        </x-slot:head>
        @foreach ($promoRows as $code)
            <tr class="hover:bg-stone-50">
                <td class="px-4 py-3 font-medium">{{ $code->code }}</td>
                <td class="px-4 py-3 text-stone-600">{{ $code->label }}{{ $code->for_game ? ' · '.__('admin.for_game') : '' }}</td>
                <td class="px-4 py-3 text-stone-500">{{ $code->expires_at?->isoFormat('LL') ?? '—' }}</td>
                <td class="px-2 py-2">
                    <x-admin.row-menu :edit="'#promo-'.$code->id" :delete="route('admin.promos.destroy', $code)" />
                </td>
            </tr>
        @endforeach
    </x-admin.data-table>

    <x-admin.dialog id="promo-create" :title="__('admin.add_code')" :open="$errors->any() && old('dialog') === 'promo-create'">
        <form method="POST" action="{{ route('admin.promos.store') }}" class="grid gap-3 sm:grid-cols-2">
            @csrf
            <input type="hidden" name="dialog" value="promo-create">
            <x-admin.field name="code" :label="__('admin.code')" :value="old('dialog') === 'promo-create' ? old('code') : ''" required />
            <x-admin.field name="label" :label="__('admin.label')" :value="old('dialog') === 'promo-create' ? old('label') : ''" required />
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" checked> {{ __('admin.active') }}</label>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="for_game" value="1"> {{ __('admin.for_game') }}</label>
            <div class="sm:col-span-2"><x-admin.button>{{ __('admin.save') }}</x-admin.button></div>
        </form>
    </x-admin.dialog>

    @foreach ($codes as $code)
        <x-admin.dialog :id="'promo-'.$code->id" :title="$code->code" :open="$errors->any() && (string) old('dialog') === 'promo-'.$code->id">
            <form method="POST" action="{{ route('admin.promos.update', $code) }}" class="grid gap-3 sm:grid-cols-2">
                @csrf
                @method('PUT')
                <input type="hidden" name="dialog" value="promo-{{ $code->id }}">
                <x-admin.field name="code" :label="__('admin.code')" :value="(string) old('dialog') === 'promo-'.$code->id ? old('code', $code->code) : $code->code" required />
                <x-admin.field name="label" :label="__('admin.label')" :value="(string) old('dialog') === 'promo-'.$code->id ? old('label', $code->label) : $code->label" required />
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked($code->is_active)> {{ __('admin.active') }}</label>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="for_game" value="1" @checked($code->for_game)> {{ __('admin.for_game') }}</label>
                <div class="sm:col-span-2"><x-admin.button>{{ __('admin.save') }}</x-admin.button></div>
            </form>
        </x-admin.dialog>
    @endforeach
@endsection
