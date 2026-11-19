@props(['title' => null])

<section {{ $attributes->merge(['class' => 'admin-surface']) }}>
    @if ($title || isset($action))
        <div class="flex items-center justify-between gap-3 px-5 pt-4">
            @if ($title)
                <h2 class="text-sm font-semibold tracking-tight">{{ $title }}</h2>
            @endif
            @isset($action)
                {{ $action }}
            @endisset
        </div>
    @endif
    <div class="px-5 py-4">{{ $slot }}</div>
</section>
