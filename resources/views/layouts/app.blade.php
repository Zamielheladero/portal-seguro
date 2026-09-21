<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Seguro')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">

    <style>
        :root {
            --verde: #1f4e44;
            --verde-oscuro: #163a32;
            --verde-claro: #e8f5f1;
            --texto: #22272b;
            --gris: #6b7280;
            --borde: #e2e5e9;
            --fondo: #f7f8fa;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--texto);
            background: var(--fondo);
            line-height: 1.55;
        }

        h1, h2, h3 {
            font-family: 'Poppins', 'Inter', sans-serif;
            color: var(--verde-oscuro);
            margin-top: 0;
        }

        h1 { font-size: 1.9rem; margin-bottom: 1.2rem; }
        h2 { font-size: 1.25rem; }

        a { color: var(--verde); text-decoration: none; }
        a:hover { text-decoration: underline; }

        header {
            background: #fff;
            border-bottom: 1px solid var(--borde);
        }

        header nav {
            max-width: 1080px;
            margin: 0 auto;
            padding: 0.9rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.4rem;
            flex-wrap: wrap;
        }

        header nav a {
            color: var(--texto);
            font-weight: 500;
            font-size: 0.95rem;
        }

        header nav a:hover { color: var(--verde); text-decoration: none; }

        header nav form { margin-left: auto; }

        header nav button {
            background: transparent;
            border: 1px solid var(--borde);
            border-radius: 6px;
            padding: 0.45rem 0.9rem;
            font-family: inherit;
            font-size: 0.9rem;
            color: var(--texto);
            cursor: pointer;
        }

        header nav button:hover { border-color: var(--verde); color: var(--verde); }

        main {
            max-width: 1080px;
            margin: 2rem auto;
            padding: 0 1.5rem 3rem;
        }

        /* Cards genéricas: cualquier <div> directo dentro de listados de
           productos/posts hereda este look sin tener que tocar cada vista */
        main > div, main form {
            background: #fff;
            border: 1px solid var(--borde);
            border-radius: 10px;
            padding: 1.5rem;
        }

        main form { max-width: 480px; margin-bottom: 1.5rem; }

        label {
            display: block;
            font-weight: 500;
            font-size: 0.9rem;
            margin: 0.9rem 0 0.3rem;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="file"],
        textarea {
            width: 100%;
            padding: 0.6rem 0.7rem;
            border: 1px solid var(--borde);
            border-radius: 6px;
            font-family: inherit;
            font-size: 0.95rem;
        }

        textarea { min-height: 90px; resize: vertical; }

        button[type="submit"] {
            margin-top: 1.2rem;
            background: var(--verde);
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 0.65rem 1.4rem;
            font-family: inherit;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
        }

        button[type="submit"]:hover { background: var(--verde-oscuro); }

        p { color: var(--gris); }

        img { border-radius: 6px; }
    </style>
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
        <a href="{{ route('productos.index') }}">Productos</a>
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