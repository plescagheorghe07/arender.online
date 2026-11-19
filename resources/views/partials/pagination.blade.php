@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('ui.pagination') }}" class="mt-10 flex flex-col gap-4 border-t border-stone-200 pt-6 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-stone-500">
            {{ __('ui.pagination_status', [
                'from' => $paginator->firstItem() ?? 0,
                'to' => $paginator->lastItem() ?? 0,
                'total' => $paginator->total(),
            ]) }}
        </p>

        <div class="flex flex-wrap items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="px-3 py-2 text-sm text-stone-300">{{ __('ui.previous') }}</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="rounded-full px-3 py-2 text-sm text-stone-700 hover:bg-white">{{ __('ui.previous') }}</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 text-sm text-stone-400">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="rounded-full bg-stone-900 px-3 py-2 text-sm text-white">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="rounded-full px-3 py-2 text-sm text-stone-700 hover:bg-white">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="rounded-full px-3 py-2 text-sm text-stone-700 hover:bg-white">{{ __('ui.next') }}</a>
            @else
                <span class="px-3 py-2 text-sm text-stone-300">{{ __('ui.next') }}</span>
            @endif
        </div>
    </nav>
@endif
