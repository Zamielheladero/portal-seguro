<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

/**
 * SEC-07 - Cabeceras de seguridad HTTP (hallazgos ZAP 17-oct-2026).
 *
 * Corrige las alertas de ZAP:
 *  - Cabecera Content Security Policy (CSP) no configurada  -> Content-Security-Policy
 *  - Falta de cabecera Anti-Clickjacking                    -> X-Frame-Options + frame-ancestors
 *  - Falta encabezado X-Content-Type-Options                -> X-Content-Type-Options: nosniff
 *  - El servidor divulga información mediante X-Powered-By   -> se elimina la cabecera
 *
 * Se registra como middleware GLOBAL (bootstrap/app.php) para que también
 * cubra las páginas de error (404, 403, 419, 500).
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // PHP la agrega por su cuenta (expose_php = On en php.ini); se quita
        // tanto del SAPI como de la respuesta de Symfony.
        if (! headers_sent()) {
            header_remove('X-Powered-By');
        }
        $response->headers->remove('X-Powered-By');

        $response->headers->set('Content-Security-Policy', $this->contentSecurityPolicy());
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // HSTS solo tiene sentido (y solo se respeta) sobre HTTPS.
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }

    /**
     * Solo se permite cargar recursos del propio sitio.
     * - Las fuentes ya no vienen de Google Fonts: se sirven desde /build con Vite.
     * - 'unsafe-inline' solo en estilos, por el bloque <style> del layout.
     *   Los scripts NO permiten código en línea (ni onclick=...), que es lo que frena un XSS.
     * - En desarrollo con "npm run dev" se permite el servidor de Vite.
     */
    private function contentSecurityPolicy(): string
    {
        $script = ["'self'"];
        $style = ["'self'", "'unsafe-inline'"];
        $connect = ["'self'"];

        if (Vite::isRunningHot()) {
            $hot = rtrim(trim((string) file_get_contents(Vite::hotFile())), '/');
            $script[] = $hot;
            $style[] = $hot;
            $connect[] = $hot;
            $connect[] = preg_replace('#^http#', 'ws', $hot);
        }

        return implode('; ', [
            "default-src 'self'",
            'script-src '.implode(' ', $script),
            'style-src '.implode(' ', $style),
            "img-src 'self' data:",
            "font-src 'self' data:",
            'connect-src '.implode(' ', $connect),
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
        ]);
    }
}
