@props(['name', 'active' => false])

<button
    type="button"
    data-record-tab="{{ $name }}"
    @class([
        'cursor-pointer border-b-2 px-3 py-2.5 text-sm font-medium whitespace-nowrap',
        'border-emerald-900 text-stone-900' => $active,
        'border-transparent text-stone-500' => ! $active,
    ])
>{{ $slot }}</button>
