<?php

use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
| SCRUM-24 (CMS-06) - Hallazgo de seguridad 16-oct:
| un visitante que se registra NO debe obtener acceso al panel de productos.
*/

function productoDePrueba(): Producto
{
    return Producto::create([
        'nombre' => 'Martillo',
        'precio' => 25000,
        'stock' => 10,
        'activo' => true,
    ]);
}

test('el registro público crea usuarios con rol cliente', function () {
    $this->post('/registro', [
        'name' => 'Visitante',
        'email' => 'visitante@example.com',
        'password' => 'Segura#Clave2026',
        'password_confirmation' => 'Segura#Clave2026',
    ])->assertRedirect(route('dashboard'));

    expect(User::where('email', 'visitante@example.com')->first()->role)->toBe('cliente');
});

test('no se puede auto-asignar un rol enviando "role" en el registro (mass assignment)', function () {
    $this->post('/registro', [
        'name' => 'Atacante',
        'email' => 'atacante@example.com',
        'password' => 'Segura#Clave2026',
        'password_confirmation' => 'Segura#Clave2026',
        'role' => 'admin',
    ]);

    expect(User::where('email', 'atacante@example.com')->first()->role)->toBe('cliente');
});

test('un cliente recibe 403 en todo el panel de productos', function () {
    $cliente = User::factory()->create();
    $producto = productoDePrueba();

    $this->actingAs($cliente)->get('/productos')->assertForbidden();
    $this->actingAs($cliente)->get('/productos/create')->assertForbidden();
    $this->actingAs($cliente)->post('/productos', [
        'nombre' => 'Hack', 'precio' => 1, 'stock' => 1,
    ])->assertForbidden();
    $this->actingAs($cliente)->get("/productos/{$producto->id}/edit")->assertForbidden();
    $this->actingAs($cliente)->put("/productos/{$producto->id}", [
        'nombre' => 'Hack', 'precio' => 1, 'stock' => 1,
    ])->assertForbidden();
    $this->actingAs($cliente)->delete("/productos/{$producto->id}")->assertForbidden();

    expect(Producto::count())->toBe(1)
        ->and($producto->fresh()->precio)->toEqual('25000.00');
});

test('un vendedor puede gestionar productos pero no eliminarlos', function () {
    $vendedor = User::factory()->vendedor()->create();
    $producto = productoDePrueba();

    $this->actingAs($vendedor)->get('/productos')->assertOk();
    $this->actingAs($vendedor)->get('/productos/create')->assertOk();
    $this->actingAs($vendedor)->post('/productos', [
        'nombre' => 'Destornillador', 'precio' => 8000, 'stock' => 5,
    ])->assertRedirect(route('productos.index'));
    $this->actingAs($vendedor)->delete("/productos/{$producto->id}")->assertForbidden();

    expect(Producto::count())->toBe(2);
});

test('un admin puede eliminar productos', function () {
    $admin = User::factory()->admin()->create();
    $producto = productoDePrueba();

    $this->actingAs($admin)->delete("/productos/{$producto->id}")
        ->assertRedirect(route('productos.index'));

    expect(Producto::count())->toBe(0);
});

test('el catálogo público sigue abierto para visitantes sin sesión', function () {
    productoDePrueba();

    $this->get('/catalogo')->assertOk()->assertSee('Martillo');
});

test('el menú solo muestra "Productos" al personal interno', function () {
    $this->actingAs(User::factory()->create())->get('/dashboard')
        ->assertOk()->assertDontSee('href="'.route('productos.index').'"', false);

    $this->actingAs(User::factory()->vendedor()->create())->get('/dashboard')
        ->assertOk()->assertSee('href="'.route('productos.index').'"', false);
});
