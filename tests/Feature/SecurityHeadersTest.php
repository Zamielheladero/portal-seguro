<?php

use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
| SEC-07 - Cabeceras de seguridad (hallazgos ZAP 17-oct-2026).
*/

dataset('paginas', [
    'inicio' => '/',
    'catálogo' => '/catalogo',
    'login' => '/login',
    'registro' => '/registro',
    'contacto' => '/contacto',
    'página que no existe (404)' => '/sitemap.xml',
]);

test('toda respuesta lleva las cabeceras de seguridad', function (string $url) {
    $response = $this->get($url);

    $response->assertHeader('X-Frame-Options', 'DENY');
    $response->assertHeader('X-Content-Type-Options', 'nosniff');
    $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    $response->assertHeaderMissing('X-Powered-By');

    $csp = $response->headers->get('Content-Security-Policy');
    expect($csp)
        ->toContain("default-src 'self'")
        ->toContain("script-src 'self'")
        ->toContain("frame-ancestors 'none'")
        ->toContain("object-src 'none'")
        ->not->toContain('unsafe-eval');

    // Los scripts no permiten código en línea
    expect(str($csp)->betweenFirst('script-src', ';')->toString())->not->toContain('unsafe-inline');
})->with('paginas');

test('el layout ya no carga recursos de Google Fonts', function () {
    $this->get('/')->assertOk()->assertDontSee('fonts.googleapis.com', false);
});

test('el botón eliminar no usa JavaScript en línea', function () {
    Producto::create(['nombre' => 'Martillo', 'precio' => 25000, 'stock' => 10, 'activo' => true]);

    $this->actingAs(User::factory()->admin()->create())
        ->get('/productos')
        ->assertOk()
        ->assertSee('data-confirm="¿Eliminar este producto?"', false)
        ->assertDontSee('onclick=', false);
});

test('el formulario de contacto no consulta la base de datos (alerta SQLi = falso positivo)', function () {
    \Illuminate\Support\Facades\DB::enableQueryLog();

    $this->from('/contacto')->post('/contacto', [
        'name' => 'ZAP',
        'email' => 'zaproxy@example.com AND 1=1 -- ',
        'message' => 'prueba',
    ])->assertRedirect('/contacto')->assertSessionHasErrors('email');

    $consultas = collect(\Illuminate\Support\Facades\DB::getQueryLog())
        ->pluck('query')
        ->reject(fn ($q) => str_contains($q, 'sessions') || str_contains($q, 'cache'));

    expect($consultas)->toBeEmpty();
});
