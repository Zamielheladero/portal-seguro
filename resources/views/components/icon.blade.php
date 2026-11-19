@props(['name', 'size' => 22])

@php
    // Íconos de trazo del diseño AgroPoste (contenido estático y de confianza).
    $paths = [
        'leaf' => '<path d="M20 4c-7 0-12 3-12 9 0 3 2 5 5 5 6 0 7-8 7-14Z"/><path d="M4 21c3-7 8-10 13-12"/>',
        'menu' => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'cart' => '<path d="M3 4h2l2.2 10.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.6L20 7H6"/><circle cx="10" cy="20" r="1"/><circle cx="17" cy="20" r="1"/>',
        'arrow' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'check' => '<path d="m5 12 4 4L19 6"/>',
        'truck' => '<path d="M3 6h11v10H3zM14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="18" cy="18" r="2"/>',
        'shield' => '<path d="M12 3 4.5 6v5c0 5 3 8.5 7.5 10 4.5-1.5 7.5-5 7.5-10V6z"/><path d="m9 12 2 2 4-5"/>',
        'phone' => '<path d="M7 3H4.5A1.5 1.5 0 0 0 3 4.5C3 13.6 10.4 21 19.5 21a1.5 1.5 0 0 0 1.5-1.5V17l-5-1-1.4 3c-4.1-1.7-7.2-4.8-8.9-8.9l3-1.4z"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>',
        'sliders' => '<path d="M4 6h16M4 12h16M4 18h16"/><circle cx="9" cy="6" r="2"/><circle cx="15" cy="12" r="2"/><circle cx="7" cy="18" r="2"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'minus' => '<path d="M5 12h14"/>',
        'whatsapp' => '<path d="M20 11.5a8 8 0 0 1-11.8 7L4 20l1.4-4A8 8 0 1 1 20 11.5Z"/><path d="M9 8c.5 3 2 4.5 5 5"/>',
        'calculator' => '<rect x="5" y="3" width="14" height="18" rx="2"/><path d="M8 7h8M9 12h.01M13 12h.01M17 12h.01M9 16h.01M13 16h.01M17 16h.01"/>',
        'x' => '<path d="m6 6 12 12M18 6 6 18"/>',
        'map' => '<path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3zM9 3v15M15 6v15"/>',
        'download' => '<path d="M12 3v12m-5-5 5 5 5-5M5 21h14"/>',
        'fence' => '<path d="M6 3v18M18 3v18M3 8h18M3 16h18"/>',
    ];
@endphp

<svg width="{{ (int) $size }}" height="{{ (int) $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" {{ $attributes }}>{!! $paths[$name] ?? '' !!}</svg>
