<?php

use App\Models\Cotizacion;
use App\Models\Producto;
use App\Models\User;
use Database\Seeders\ProductoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
| Diseño AgroPoste: catálogo con filtros, cotización pública y panel de cotizaciones.
*/

beforeEach(function () {
    $this->seed(ProductoSeeder::class);
});

function producto(string $nombre): Producto
{
    return Producto::where('nombre', $nombre)->firstOrFail();
}

function datosEntrega(array $cambios = []): array
{
    return [
        'nombre_cliente' => 'José Manuel Rodríguez',
        'telefono' => '310 555 1234',
        'vereda' => 'Vereda Centro',
        'indicaciones' => 'Después de la escuela',
        ...$cambios,
    ];
}

function enviarCotizacion($test, string $nombre = 'Estacón de eucalipto', int $cantidad = 100): Cotizacion
{
    $test->post(route('cotizacion.agregar'), ['producto_id' => producto($nombre)->id, 'cantidad' => $cantidad]);
    $test->post(route('cotizacion.entrega'), datosEntrega());
    $test->post(route('cotizacion.store'));

    return Cotizacion::latest('id')->firstOrFail();
}

dataset('paginas_agroposte', [
    'inicio' => '/',
    'catálogo' => '/catalogo',
    'catálogo filtrado' => '/catalogo?tratamiento[]=inmunizado&orden=menor-precio&q=eu',
    'cotización' => '/cotizacion',
    'transporte' => '/transporte',
    'garantías' => '/garantias',
    'asesoría' => '/asesoria?uso=corral&suelo=seco',
]);

test('las páginas públicas cargan sin estilos en línea ni Google Fonts', function (string $url) {
    $html = $this->get($url)->assertOk()->getContent();

    expect($html)->not->toContain('<style')
        ->and(preg_match('/\sstyle\s*=/i', $html))->toBe(0)
        ->and($html)->not->toContain('fonts.googleapis.com');
})->with('paginas_agroposte');

test('el detalle del producto carga sin estilos en línea', function () {
    $html = $this->get(route('catalogo.show', producto('Estacón de eucalipto')))->assertOk()->getContent();

    expect(preg_match('/\sstyle\s*=/i', $html))->toBe(0)
        ->and($html)->toContain('12–15 años');
});

test('el inicio muestra productos disponibles', function () {
    Producto::create(['nombre' => 'Producto agotado', 'precio' => 1000, 'stock' => 0, 'activo' => true]);

    $this->get('/')->assertSee('Grapa galvanizada')->assertDontSee('Producto agotado');
});

test('el catálogo filtra por tratamiento y ordena por precio', function () {
    $this->get('/catalogo?tratamiento[]=galvanizado&orden=menor-precio')
        ->assertOk()
        ->assertSeeInOrder(['Grapa galvanizada', 'Alambre de púas'])
        ->assertDontSee('Vareta de pino');
});

test('el catálogo ignora filtros y órdenes fuera de la lista blanca', function () {
    $this->get("/catalogo?tratamiento[]=1' OR 1=1--&orden=precio;drop table productos")
        ->assertOk()
        ->assertViewHas('orden', 'recientes')
        ->assertViewHas('filtrosActivos', 0);

    expect(Producto::count())->toBe(6);
});

test('el catálogo busca por texto y escapa lo que se escribe', function () {
    $this->get('/catalogo?q=alambre')->assertSee('Alambre de púas')->assertDontSee('Tabla para corral');

    $this->get('/catalogo?q=<script>alert(1)</script>')
        ->assertOk()
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertSee('&lt;script&gt;', false);
});

test('se agrega un producto a la cotización y se cambia la cantidad', function () {
    $vareta = producto('Vareta de pino');

    $this->post(route('cotizacion.agregar'), ['producto_id' => $vareta->id, 'cantidad' => 50])->assertRedirect();
    $this->get(route('cotizacion.show'))->assertSee('Vareta de pino')->assertSee('$ 840.000');

    $this->patch(route('cotizacion.actualizar', $vareta), ['cantidad' => 0])->assertRedirect(route('cotizacion.show'));
    $this->get(route('cotizacion.show'))->assertSee('Todavía no ha agregado productos');
});

test('no se pueden cotizar productos inactivos o sin stock', function () {
    $agotado = Producto::create(['nombre' => 'Agotado', 'precio' => 1000, 'stock' => 0, 'activo' => true]);
    $inactivo = Producto::create(['nombre' => 'Inactivo', 'precio' => 1000, 'stock' => 5, 'activo' => false]);

    $this->post(route('cotizacion.agregar'), ['producto_id' => $agotado->id])->assertSessionHasErrors('producto_id');
    $this->post(route('cotizacion.agregar'), ['producto_id' => $inactivo->id])->assertSessionHasErrors('producto_id');
});

test('el precio sale de la base de datos y no del formulario', function () {
    $this->post(route('cotizacion.agregar'), [
        'producto_id' => producto('Vareta de pino')->id,
        'cantidad' => 10,
        'precio' => 1,
    ]);

    $this->get(route('cotizacion.show'))->assertSee('$ 168.000');
});

test('la calculadora sugiere estacones para la cerca', function () {
    $this->post(route('cotizacion.calcular'), ['metros' => 500, 'distancia' => '2.5'])
        ->assertRedirect(route('cotizacion.show'));

    expect(session('cotizacion.items'))->toBe([producto('Estacón de eucalipto')->id => 202]);
});

test('el flujo completo guarda la cotización con descuento por volumen', function () {
    $cotizacion = enviarCotizacion($this);

    expect($cotizacion->codigo)->toStartWith('AGP-')
        ->and($cotizacion->subtotal)->toBe(2290000)
        ->and($cotizacion->descuento)->toBe(183200)
        ->and($cotizacion->total)->toBe(2290000 - 183200 + 280000)
        ->and($cotizacion->estado)->toBe('pendiente')
        ->and($cotizacion->items)->toHaveCount(1);

    $this->get(route('cotizacion.confirmacion', $cotizacion->codigo))->assertOk()->assertSee('¡Listo, José!', false);
    $this->get(route('cotizacion.show'))->assertSee('Todavía no ha agregado productos');
});

test('la entrega exige vereda y teléfono válido', function () {
    $this->post(route('cotizacion.entrega'), datosEntrega(['vereda' => '']))
        ->assertSessionHasErrors('vereda');

    $this->post(route('cotizacion.entrega'), datosEntrega(['telefono' => '<script>']))
        ->assertSessionHasErrors('telefono');
});

test('no se envía una cotización sin datos de entrega', function () {
    $this->post(route('cotizacion.agregar'), ['producto_id' => producto('Vareta de pino')->id]);

    $this->post(route('cotizacion.store'))->assertRedirect(route('cotizacion.show', ['paso' => 2]));

    expect(Cotizacion::count())->toBe(0);
});

test('nadie más puede ver la confirmación de una cotización ajena (IDOR)', function () {
    $cotizacion = enviarCotizacion($this);

    $this->flushSession();

    $this->get(route('cotizacion.confirmacion', $cotizacion->codigo))->assertNotFound();
});

test('un cliente no puede ver las cotizaciones del panel', function () {
    $cotizacion = enviarCotizacion($this);
    $cliente = User::factory()->create();

    $this->actingAs($cliente)->get(route('cotizaciones.index'))->assertForbidden();
    $this->actingAs($cliente)->get(route('cotizaciones.show', $cotizacion))->assertForbidden();
    $this->actingAs($cliente)->patch(route('cotizaciones.update', $cotizacion), ['estado' => 'aprobada'])->assertForbidden();
});

test('el personal interno ve las cotizaciones y cambia su estado', function () {
    $cotizacion = enviarCotizacion($this);
    $vendedor = User::factory()->vendedor()->create();

    $this->actingAs($vendedor)->get(route('cotizaciones.index'))->assertOk()->assertSee($cotizacion->codigo);
    $this->actingAs($vendedor)->get(route('cotizaciones.show', $cotizacion))->assertOk()->assertSee('José Manuel Rodríguez');

    $this->actingAs($vendedor)->patch(route('cotizaciones.update', $cotizacion), ['estado' => 'aprobada'])
        ->assertRedirect(route('cotizaciones.show', $cotizacion));
    expect($cotizacion->fresh()->estado)->toBe('aprobada');

    $this->actingAs($vendedor)->patch(route('cotizaciones.update', $cotizacion), ['estado' => 'hackeada'])
        ->assertSessionHasErrors('estado');
});

test('el panel guarda los datos del catálogo de un producto', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post(route('productos.store'), [
        'nombre' => 'Poste de cedro',
        'precio' => 30000,
        'stock' => 20,
        'activo' => 1,
        'categoria' => 'estacones',
        'tratamiento' => 'natural',
        'uso' => 'corral',
        'material' => 'Cedro',
        'medida' => '2,5 m',
        'largo_m' => 2.5,
        'durabilidad_min' => 10,
        'durabilidad_max' => 12,
    ])->assertRedirect(route('productos.index'));

    expect(producto('Poste de cedro')->categoria)->toBe('estacones');

    $this->actingAs($admin)->post(route('productos.store'), [
        'nombre' => 'Producto raro',
        'precio' => 1,
        'stock' => 1,
        'categoria' => 'armas',
    ])->assertSessionHasErrors('categoria');
});

test('un producto sin datos del catálogo se sigue mostrando', function () {
    $martillo = Producto::create(['nombre' => 'Martillo', 'precio' => 25000, 'stock' => 10, 'activo' => true]);

    $this->get(route('catalogo.show', $martillo))->assertOk()->assertSee('Martillo');
    $this->get('/catalogo')->assertSee('Martillo');
});

test('la asesoría recomienda un producto disponible', function () {
    $this->get('/asesoria?uso=corral&suelo=seco')->assertSee('Le recomendamos')->assertSee('Poste de guayacán');
    $this->get('/asesoria?uso=otro&suelo=seco')->assertDontSee('Le recomendamos');
});

test('el sitio solo ofrece entregas en Ubaté', function () {
    $this->get('/transporte')->assertSee('Ubaté, Cundinamarca')->assertDontSee('Boyacá');
    $this->get('/')->assertDontSee('Suesca')->assertDontSee('Boyacá');
});

test('la cotización siempre se guarda en Ubaté aunque envíen otro municipio', function () {
    $this->post(route('cotizacion.agregar'), ['producto_id' => producto('Vareta de pino')->id]);
    $this->post(route('cotizacion.entrega'), datosEntrega(['departamento' => 'Boyacá', 'municipio' => 'Tunja']));
    $this->post(route('cotizacion.store'));

    $cotizacion = Cotizacion::sole();

    expect($cotizacion->municipio)->toBe('Ubaté')
        ->and($cotizacion->departamento)->toBe('Cundinamarca')
        ->and($cotizacion->vereda)->toBe('Vereda Centro');
});
