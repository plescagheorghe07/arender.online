@props(['initial' => 'ro', 'compact' => false])

<div {{ $attributes->merge(['class' => '']) }} data-tabs data-tabs-initial="{{ $initial }}">
    <div @class(['admin-track', 'text-xs' => $compact])>
        <button type="button" data-tab="ro" @class(['cursor-pointer rounded-full font-medium text-stone-500', 'px-2.5 py-1 text-xs' => $compact, 'px-3 py-1 text-sm' => ! $compact])>{{ __('admin.locale_ro') }}</button>
        <button type="button" data-tab="ru" @class(['cursor-pointer rounded-full font-medium text-stone-500', 'px-2.5 py-1 text-xs' => $compact, 'px-3 py-1 text-sm' => ! $compact])>{{ __('admin.locale_ru') }}</button>
    </div>
    <div data-tab-panel="ro" @class(['pt-2' => $compact, 'pt-4' => ! $compact])>{{ $ro }}</div>
    <div data-tab-panel="ru" @class(['hidden', 'pt-2' => $compact, 'pt-4' => ! $compact])>{{ $ru }}</div>
</div>
