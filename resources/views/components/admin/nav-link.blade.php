@props(['href', 'icon', 'active' => false])

<a href="{{ $href }}" @class(['flex items-center gap-2 rounded-xl px-3 py-2 text-sm whitespace-nowrap', 'bg-[#ebe7e2] font-medium text-stone-950' => $active, 'text-stone-600 hover:bg-[#f6f4f1] hover:text-stone-950' => ! $active])>
    <x-admin.icon :name="$icon" />
    <span>{{ $slot }}</span>
</a>
