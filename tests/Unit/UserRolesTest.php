<?php

use App\Models\User;

/*
| Prueba Unit (sin base de datos ni HTTP): lógica de roles del modelo User.
*/

function usuarioConRol(?string $rol): User
{
    $user = new User;

    if ($rol !== null) {
        $user->forceFill(['role' => $rol]);
    }

    return $user;
}

test('un usuario nuevo es cliente por defecto', function () {
    expect(usuarioConRol(null)->role)->toBe(User::ROLE_CLIENTE);
});

test('isAdmin, isVendedor e isStaff responden según el rol', function (string $rol, bool $admin, bool $vendedor, bool $staff) {
    $user = usuarioConRol($rol);

    expect($user->isAdmin())->toBe($admin)
        ->and($user->isVendedor())->toBe($vendedor)
        ->and($user->isStaff())->toBe($staff);
})->with([
    'admin' => ['admin', true, false, true],
    'vendedor' => ['vendedor', false, true, true],
    'cliente' => ['cliente', false, false, false],
    'rol desconocido' => ['superusuario', false, false, false],
]);

test('el rol no se puede asignar de forma masiva', function () {
    expect((new User)->isFillable('role'))->toBeFalse();
});
