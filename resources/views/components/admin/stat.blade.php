@props(['href', 'label', 'value', 'tone' => 'white'])

<a href="{{ $href }}" class="admin-surface block px-5 py-4">
    <p class="text-sm text-stone-500">{{ $label }}</p>
    <p @class(['mt-2 text-2xl font-semibold tracking-tight', 'text-emerald-800' => $tone === 'emerald'])>{{ $value }}</p>
</a>
