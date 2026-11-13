<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Seguro')</title>
    @vite(['resources/css/app.css', 'resources/css/layout.css', 'resources/js/app.js'])
</head>
<body>
    <header>
       <nav>
    <a href="{{ route('home') }}">Inicio</a>
    <a href="{{ route('about') }}">Acerca</a>
    <a href="{{ route('contact') }}">Contacto</a>
    <a href="{{ route('catalogo.index') }}">Catálogo</a>

    @auth
        <a href="{{ route('posts.index') }}">Publicaciones</a>
        @can('viewAny', App\Models\Producto::class)
            <a href="{{ route('productos.index') }}">Productos</a>
        @endcan
        <a href="{{ route('dashboard') }}">Mi cuenta</a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Cerrar sesión</button>
        </form>
    @else
        <a href="{{ route('login') }}">Iniciar sesión</a>
        <a href="{{ route('register') }}">Registrarme</a>
    @endauth
        </nav>
    </header>
    <main>
        @yield('content')
    </main>
</body>
</html>