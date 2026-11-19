@props(['name'])

<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true" {{ $attributes->merge(['class' => 'h-4 w-4']) }}>
    @switch($name)
        @case('spark')
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.5 13.6 9.2 19.5 11 13.6 12.8 12 18.5 10.4 12.8 4.5 11 10.4 9.2 12 3.5z"></path>
            @break
        @case('grid')
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 5h5.5v5.5H5zM13.5 5H19v5.5h-5.5zM5 13.5h5.5V19H5zM13.5 13.5H19V19h-5.5z"></path>
            @break
        @case('news')
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 5.5h9.5A2.5 2.5 0 0 1 18 8v10.5H8A2.5 2.5 0 0 1 5.5 16V7A1.5 1.5 0 0 1 7 5.5z"></path>
            <path stroke-linecap="round" d="M8.5 9.5h6M8.5 12.5h6M8.5 15.5h3"></path>
            @break
        @case('game')
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 16.5 6.2 14a4 4 0 0 1 3.2-6.5h5.2A4 4 0 0 1 17.8 14L16 16.5"></path>
            <path stroke-linecap="round" d="M9 10.5v2M8 11.5h2M15.2 10.8h.01M16.8 12.2h.01"></path>
            @break
        @case('phone')
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 3h3l1.5 4-2 1.2a12 12 0 0 0 6.3 6.3L17 12.5 21 14v3a2 2 0 0 1-2.2 2A17 17 0 0 1 5 6.2 2 2 0 0 1 7 3z"></path>
            @break
        @case('bolt')
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 3 6.5 13H12l-1 8 6.5-10H12l1-8z"></path>
            @break
        @case('truck')
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.5 7.5h10v8h-10zM13.5 10.5h4.2L20.5 14v1.5h-7"></path>
            <path stroke-linecap="round" d="M7 18.2a1.4 1.4 0 1 0 0-2.8 1.4 1.4 0 0 0 0 2.8zM16.5 18.2a1.4 1.4 0 1 0 0-2.8 1.4 1.4 0 0 0 0 2.8z"></path>
            @break
        @case('shield')
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.5 19 6.2v5.4c0 4.2-2.8 6.8-7 8.4-4.2-1.6-7-4.2-7-8.4V6.2L12 3.5z"></path>
            @break
        @case('check')
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12.5 9.2 17 19 7"></path>
            @break
        @case('box')
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 8.5 12 4.5 20 8.5 12 12.5 4 8.5z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 8.5V16l8 4 8-4V8.5M12 12.5V20"></path>
            @break
        @default
            <circle cx="12" cy="12" r="7"></circle>
    @endswitch
</svg>
