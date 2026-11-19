@props(['name' => 'spark'])

<svg {{ $attributes->merge(['class' => 'h-4 w-4', 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.8', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'aria-hidden' => 'true']) }}>
    @switch($name)
        @case('grid')
            <rect x="4" y="4" width="6.5" height="6.5" rx="1.5"></rect>
            <rect x="13.5" y="4" width="6.5" height="6.5" rx="1.5"></rect>
            <rect x="4" y="13.5" width="6.5" height="6.5" rx="1.5"></rect>
            <rect x="13.5" y="13.5" width="6.5" height="6.5" rx="1.5"></rect>
            @break
        @case('news')
            <path d="M7 4.5h8.5L19 8v11.5H7z"></path>
            <path d="M15 4.8V8h3.2"></path>
            <path d="M10 12h6M10 15.5h4"></path>
            @break
        @case('game')
            <path d="M8 15.5 6.2 18a2 2 0 0 1-3.2-1.6l.8-4.2A5 5 0 0 1 8.7 8h6.6a5 5 0 0 1 4.9 4.2l.8 4.2a2 2 0 0 1-3.2 1.6L16 15.5Z"></path>
            <path d="M8.5 11.5v2.2M7.4 12.6h2.2M16.2 12h.1M18 13.6h.1"></path>
            @break
        @case('phone')
            <path d="M7 3h3l1.5 4-2 1.2a12 12 0 0 0 6.3 6.3L17 12.5 21 14v3a2 2 0 0 1-2.2 2A17 17 0 0 1 5 6.2 2 2 0 0 1 7 3z"></path>
            @break
        @case('bolt')
            <path d="M13 3 5.5 13.5h6L10 21l8-11h-6L13 3z"></path>
            @break
        @case('truck')
            <path d="M3 7h11v8H3z"></path>
            <path d="M14 10h4l3 3v2h-7"></path>
            <circle cx="7" cy="17.5" r="1.5"></circle>
            <circle cx="17" cy="17.5" r="1.5"></circle>
            @break
        @case('shield')
            <path d="M12 3.5 19 6.5v5.2c0 4.2-2.8 7.2-7 8.8-4.2-1.6-7-4.6-7-8.8V6.5L12 3.5z"></path>
            <path d="m9 12 2 2 4-4"></path>
            @break
        @case('pen')
            <path d="M14 5.5 18.5 10 9 19.5H4.5V15L14 5.5z"></path>
            <path d="m12.5 7 4.5 4.5"></path>
            @break
        @case('package')
            <path d="M4 8 12 4l8 4-8 4L4 8z"></path>
            <path d="M4 8v8l8 4 8-4V8"></path>
            <path d="M12 12v8"></path>
            @break
        @case('clock')
            <circle cx="12" cy="12" r="8"></circle>
            <path d="M12 8v4.5l3 2"></path>
            @break
        @case('mail')
            <rect x="3.5" y="5.5" width="17" height="13" rx="2"></rect>
            <path d="m4 7 8 6 8-6"></path>
            @break
        @case('whatsapp')
            <path d="M12 4.2a7.3 7.3 0 0 0-6.3 10.9L5 19.5l4.5-.7A7.3 7.3 0 1 0 12 4.2z"></path>
            <path d="M9.2 9.4c.2-.4.4-.4.6-.4h.4c.2 0 .3 0 .5.4l.6 1.4c.1.3 0 .4-.1.6l-.3.4c-.1.2-.3.4 0 .7.2.3.9 1.4 2 2.1 1 .7 1.4.6 1.7.5l.7-.3c.2-.1.4 0 .6.1l1.2.7c.2.1.3.2.3.5 0 .6-.5 1.5-1.1 1.6-.4.1-1 .1-2.4-.3-1.7-.5-3.3-1.9-4.3-3.5-.5-.8-.9-1.8-.9-2.6 0-.5.4-1.1.8-1.3z"></path>
            @break
        @case('telegram')
            <path d="M20 5.5 4.8 11.2c-1 .4-1 1.1-.2 1.4l3.9 1.2 1.5 4.6c.2.5.5.6 1 .3l2.2-1.9 4.3 3.2c.5.3.9.1 1-.5L20.8 6.3c.3-.9-.3-1.3-.8-.8z"></path>
            <path d="m9.2 13.7 8.3-5.2"></path>
            @break
        @case('instagram')
            <rect x="4.5" y="4.5" width="15" height="15" rx="4"></rect>
            <circle cx="12" cy="12" r="3.5"></circle>
            <circle cx="16.8" cy="7.2" r="0.9" fill="currentColor" stroke="none"></circle>
            @break
        @case('tiktok')
            <path d="M14 4.5c.6 1.8 1.8 3 3.8 3.3v2.5c-1.3 0-2.5-.4-3.5-1v5.4a4.8 4.8 0 1 1-4.8-4.8c.3 0 .6 0 .9.1v2.6a2.2 2.2 0 1 0 1.5 2.1V4.5H14z"></path>
            @break
        @case('facebook')
            <path d="M14 8.5h2.2V5.8H14c-2.2 0-3.7 1.5-3.7 3.8v1.7H8.2v2.8h2.1V19h3V14.1h2.3l.4-2.8H13.3V9.8c0-.8.3-1.3.7-1.3z"></path>
            @break
        @default
            <path d="M12 3.5 13.8 9H19l-4.2 3.2L16.4 18 12 14.8 7.6 18l1.6-5.8L5 9h5.2L12 3.5z"></path>
    @endswitch
</svg>
