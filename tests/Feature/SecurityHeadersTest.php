<?php

use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

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
    DB::enableQueryLog();

    $this->from('/contacto')->post('/contacto', [
        'name' => 'ZAP',
        'email' => 'zaproxy@example.com AND 1=1 -- ',
        'message' => 'prueba',
    ])->assertRedirect('/contacto')->assertSessionHasErrors('email');

    $consultas = collect(DB::getQueryLog())
        ->pluck('query')
        ->reject(fn ($q) => str_contains($q, 'sessions') || str_contains($q, 'cache'));

    expect($consultas)->toBeEmpty();
});

/*
| Hallazgo ZAP escaneo 2 (17-oct-2026): "CSP: style-src unsafe-inline".
| Los bloques <style> se movieron a resources/css y la CSP ya no permite estilos en línea.
*/

test('la CSP no permite scripts ni estilos en línea', function (string $url) {
    $csp = $this->get($url)->headers->get('Content-Security-Policy');

    expect($csp)->toContain("style-src 'self'")
        ->and($csp)->toContain("script-src 'self'")
        ->and($csp)->not->toContain('unsafe-inline')
        ->and($csp)->not->toContain('unsafe-eval');
})->with('paginas');

test('las páginas no traen bloques <style> ni atributos style en línea', function (string $url) {
    $html = $this->get($url)->getContent();

    expect($html)->not->toContain('<style')
        ->and(preg_match('/\sstyle\s*=/i', $html))->toBe(0);
})->with('paginas');

test('el detalle del catálogo solo muestra "Editar" a quien puede editar', function () {
    $producto = Producto::create(['nombre' => 'Llave inglesa', 'precio' => 30000, 'stock' => 4, 'activo' => true]);
    $editar = route('productos.edit', $producto);

    $this->get("/catalogo/{$producto->id}")->assertDontSee($editar);
    $this->actingAs(User::factory()->create())->get("/catalogo/{$producto->id}")->assertDontSee($editar);
    $this->actingAs(User::factory()->vendedor()->create())->get("/catalogo/{$producto->id}")->assertSee($editar);
});
