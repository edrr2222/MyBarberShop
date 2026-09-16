{{-- Espera $plataforma (instagram|facebook|tiktok|whatsapp|web|otro) --}}
@switch($plataforma)
    @case('instagram')
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="3" y="3" width="18" height="18" rx="5" stroke="currentColor" stroke-width="1.7"/>
            <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.7"/>
            <circle cx="17.2" cy="6.8" r="1.1" fill="currentColor"/>
        </svg>
        @break
    @case('facebook')
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15 8.5h2V5.2c-.35-.05-1.55-.15-2.95-.15-2.92 0-4.92 1.79-4.92 5.08V13H6.4v3.7h2.73V22h3.8v-5.3h2.72l.43-3.7h-3.15V10.5c0-1.07.29-1.8 1.87-1.8Z" fill="currentColor"/>
        </svg>
        @break
    @case('tiktok')
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M14 4c.4 2.2 1.9 3.7 4 4v2.7c-1.5 0-2.9-.45-4-1.25V15a5 5 0 1 1-5-5c.2 0 .5 0 .7.05v2.7a2.3 2.3 0 1 0 1.7 2.2V4h2.6Z" fill="currentColor"/>
        </svg>
        @break
    @case('whatsapp')
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 3a9 9 0 0 0-7.8 13.5L3 21l4.6-1.2A9 9 0 1 0 12 3Z" stroke="currentColor" stroke-width="1.5"/>
            <path d="M8.5 8.3c.2-.5.5-.5.8-.5h.6c.2 0 .4 0 .6.4l.7 1.6c.1.2 0 .4-.1.6l-.5.6c-.1.2-.2.3-.1.5.4.8 1.5 2 2.3 2.3.2.1.4 0 .5-.1l.6-.6c.2-.2.4-.2.6-.1l1.5.8c.3.15.4.3.4.5v.6c0 .3 0 .6-.5.9-.6.35-1.5.5-2.4.2-2.1-.65-4.3-2.8-5-4.9-.3-.9-.2-1.8.1-2.4Z" fill="currentColor"/>
        </svg>
        @break
    @case('web')
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/>
            <ellipse cx="12" cy="12" rx="4" ry="9" stroke="currentColor" stroke-width="1.6"/>
            <path d="M3 12h18" stroke="currentColor" stroke-width="1.6"/>
        </svg>
        @break
    @default
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10.5 13.5 13.5 10.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            <path d="M9 15 6.5 17.5a3 3 0 0 1-4.2-4.2L4.8 10.8M15 9l2.5-2.5a3 3 0 0 1 4.2 4.2L19.2 13.2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
        </svg>
@endswitch
