@props(['initial' => null])

<div
    data-record-tabs
    @if ($initial) data-record-initial="{{ $initial }}" @endif
    {{ $attributes->merge(['class' => 'admin-surface overflow-hidden']) }}
>
    {{ $slot }}
</div>
