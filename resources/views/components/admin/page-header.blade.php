@props(['title'])

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center justify-between gap-3']) }}>
    <div class="min-w-0">
        <h1 class="truncate text-2xl font-semibold tracking-tight">{{ $title }}</h1>
        @isset($meta)
            <p class="mt-1 text-sm text-stone-500">{{ $meta }}</p>
        @endisset
    </div>
    @isset($actions)
        <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
    @endisset
</div>
