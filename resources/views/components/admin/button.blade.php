@props(['href' => null, 'variant' => 'primary'])

@php
    $classes = match ($variant) {
        'dark' => 'inline-flex cursor-pointer items-center justify-center rounded-xl bg-stone-900 px-4 py-2 text-sm font-medium text-white',
        'ghost' => 'inline-flex cursor-pointer items-center justify-center rounded-xl px-3 py-2 text-sm font-medium text-stone-600 hover:bg-[#ebe7e2]',
        'danger' => 'inline-flex cursor-pointer items-center justify-center rounded-xl bg-red-700 px-4 py-2 text-sm font-medium text-white',
        default => 'inline-flex cursor-pointer items-center justify-center rounded-xl bg-emerald-800 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-900',
    };
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['class' => $classes, 'type' => 'submit']) }}>{{ $slot }}</button>
@endif
