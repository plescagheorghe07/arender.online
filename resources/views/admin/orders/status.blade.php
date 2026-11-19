@php
    $tones = [
        'new' => 'bg-emerald-50 text-emerald-900',
        'confirmed' => 'bg-amber-50 text-amber-900',
        'done' => 'bg-stone-100 text-stone-700',
        'cancelled' => 'bg-red-50 text-red-800',
    ];
@endphp
<span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $tones[$status] ?? 'bg-stone-100 text-stone-700' }}">{{ __("admin.status_$status") }}</span>
