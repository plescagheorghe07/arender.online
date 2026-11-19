@props(['isEmpty' => false, 'empty' => null, 'plain' => false])

<div {{ $attributes->merge(['class' => $plain ? '' : 'admin-surface overflow-hidden']) }}>
    @isset($toolbar)
        <div class="flex flex-wrap items-center gap-2 px-4 py-3">{{ $toolbar }}</div>
    @endisset

    @if ($isEmpty)
        <p class="px-4 py-10 text-sm text-stone-500">{{ $empty ?? __('admin.empty') }}</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                @isset($head)
                    <thead class="text-xs font-medium text-stone-500">
                        <tr>{{ $head }}</tr>
                    </thead>
                @endisset
                <tbody>{{ $slot }}</tbody>
            </table>
        </div>
    @endif
</div>
