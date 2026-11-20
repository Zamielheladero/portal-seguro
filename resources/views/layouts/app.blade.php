<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Estacones, postes, madera y alambre para cercas y corrales con entrega en Ubaté, Cundinamarca.">
    <title>@yield('title', 'Inicio') · AgroPoste</title>
    {{-- SEC-07: fuentes locales (Vite) y sin estilos ni scripts en línea. --}}
    @vite(['resources/css/app.css', 'resources/css/layout.css', 'resources/js/app.js'])
</head>
<body>
    @php
        $enlaces = [
            ['Catálogo', 'catalogo.index', 'catalogo.*'],
            ['Cotizar', 'cotizacion.show', 'cotizacion.*'],
            ['Garantías', 'garantias', 'garantias'],
            ['Transporte', 'transporte', 'transporte'],
            ['Asesoría', 'asesoria', 'asesoria'],
        ];
    @endphp

    @auth
        {{-- Accesos de la cuenta y del panel (CMS) --}}
        <div class="barra-cuenta">
            <div class="container">
                <span>Hola, {{ auth()->user()->name }}</span>
                <nav aria-label="Mi cuenta">
                    <a href="{{ route('posts.index') }}">Publicaciones</a>
                    @can('viewAny', App\Models\Producto::class)
                        <a href="{{ route('productos.index') }}">Productos</a>
                        <a href="{{ route('cotizaciones.index') }}">Cotizaciones</a>
                    @endcan
                    <a href="{{ route('dashboard') }}">Mi cuenta</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">Cerrar sesión</button>
                    </form>
                </nav>
            </div>
        </div>
    @endauth

    <header class="header">
        <div class="header-inner">
            <x-logo />

            <nav class="desktop-nav" aria-label="Principal">
                @foreach ($enlaces as [$texto, $ruta, $patron])
                    <a href="{{ route($ruta) }}" @class(['active' => request()->routeIs($patron)])>{{ $texto }}</a>
                @endforeach
            </nav>

            <div class="header-actions">
                @guest
                    <a href="{{ route('login') }}" class="enlace-ingresar">Ingresar</a>
                @endguest
                <a href="{{ route('cotizacion.show') }}" class="cart-button" aria-label="{{ $cantidadCotizacion ?? 0 }} productos en cotización">
                    <x-icon name="cart" />
                    <b>{{ $cantidadCotizacion ?? 0 }}</b>
                </a>
                <a href="{{ route('cotizacion.show') }}" class="btn btn-amber"><span>Cotizar ahora</span></a>
                <button type="button" class="menu-button" data-menu-toggle aria-expanded="false" aria-controls="menu-movil" aria-label="Abrir menú">
                    <x-icon name="menu" data-icon-open />
                    <x-icon name="x" data-icon-close hidden />
                </button>
            </div>
        </div>

        <nav class="mobile-menu" id="menu-movil" hidden aria-label="Menú móvil">
            @foreach ($enlaces as [$texto, $ruta])
                <a href="{{ route($ruta) }}">{{ $texto }} <x-icon name="arrow" :size="18" /></a>
            @endforeach
            @guest
                <a href="{{ route('login') }}">Iniciar sesión <x-icon name="arrow" :size="18" /></a>
                <a href="{{ route('register') }}">Registrarme <x-icon name="arrow" :size="18" /></a>
            @endguest
        </nav>
    </header>

    @if (session('aviso'))
        <div class="flash" role="status">
            <div class="container">
                <x-icon name="check" :size="18" />
                <span>{{ session('aviso') }}</span>
                @unless (request()->routeIs('cotizacion.*'))
                    <a href="{{ route('cotizacion.show') }}">Ver cotización</a>
                @endunless
            </div>
        </div>
    @endif

    @hasSection('pagina_completa')
        @yield('content')
    @else
        {{-- Páginas del CMS (panel, cuenta, publicaciones, formularios) --}}
        <main class="contenido">
            @yield('content')
        </main>
    @endif

    <footer>
        <div class="container footer-grid">
            <div>
                <x-logo />
                <p>Materiales confiables para cercas, corrales y proyectos del campo.</p>
                <span class="footer-contact">
                    <x-icon name="phone" :size="18" /> {{ config('agroposte.phone') }}
                </span>
            </div>
            <div>
                <h4>Productos</h4>
                <a href="{{ route('catalogo.index', ['categoria' => ['estacones']]) }}">Estacones</a>
                <a href="{{ route('catalogo.index', ['categoria' => ['estacones', 'varetas']]) }}">Postes y varetas</a>
                <a href="{{ route('catalogo.index', ['categoria' => ['tablas']]) }}">Tablas para corral</a>
                <a href="{{ route('catalogo.index', ['categoria' => ['alambre']]) }}">Alambre y accesorios</a>
            </div>
            <div>
                <h4>Le ayudamos</h4>
                <a href="{{ route('asesoria') }}">Asesoría técnica</a>
                <a href="{{ route('transporte') }}">Transporte y cobertura</a>
                <a href="{{ route('garantias') }}">Garantías</a>
                <a href="{{ route('contact') }}">Contacto</a>
            </div>
            <div>
                <h4>Zona de cobertura</h4>
                <p>Ubaté, Cundinamarca</p>
                <p>Casco urbano y veredas.</p>
            </div>
        </div>
        <div class="container footer-bottom">
            <span>© {{ now()->year }} AgroPoste. Hecho para el campo colombiano.</span>
            <a href="{{ route('about') }}">Acerca del proyecto</a>
        </div>
    </footer>

    <nav class="bottom-nav" aria-label="Accesos rápidos">
        @foreach ([['catalogo.index', 'catalogo.*', 'fence', 'Catálogo'], ['cotizacion.show', 'cotizacion.*', 'calculator', 'Cotizar'], ['asesoria', 'asesoria', 'phone', 'Asesoría']] as [$ruta, $patron, $icono, $texto])
            <a href="{{ route($ruta) }}" @class(['active' => request()->routeIs($patron)])>
                <x-icon :name="$icono" />
                <span>{{ $texto }}</span>
            </a>
        @endforeach
        <a href="https://wa.me/{{ config('agroposte.whatsapp') }}" class="whatsapp-nav" target="_blank" rel="noopener noreferrer">
            <x-icon name="whatsapp" />
            <span>WhatsApp</span>
        </a>
    </nav>
</body>
</html>
