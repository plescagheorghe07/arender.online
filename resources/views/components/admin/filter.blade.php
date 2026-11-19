@props(['href', 'active' => false])

<a href="{{ $href }}" @class(['inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm', 'bg-white font-medium text-stone-950 shadow-sm' => $active, 'text-stone-600 hover:text-stone-950' => ! $active])>{{ $slot }}</a>
