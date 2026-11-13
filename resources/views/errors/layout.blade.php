{{--
    Plantilla propia para páginas de error. Reemplaza las de Laravel, que traen
    un bloque <style> en línea que la CSP (style-src 'self') bloquea.
    No usa sesión ni csrf_token(): un 404 se renderiza fuera del grupo "web".
--}}
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') · Portal Seguro</title>
    @vite(['resources/css/app.css', 'resources/css/errores.css'])
</head>
<body class="pagina-error">
    <main class="error-caja">
        <p class="error-codigo">@yield('code')</p>
        <h1>@yield('title')</h1>
        <p>@yield('message')</p>
        <a href="{{ url('/') }}" class="error-volver">Volver al inicio</a>
    </main>
</body>
</html>
