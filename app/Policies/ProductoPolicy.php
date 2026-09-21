<?php

namespace App\Policies;

use App\Models\Producto;
use App\Models\User;

class ProductoPolicy
{
    /**
     * Cualquier usuario autenticado (vendedor o admin) puede editar
     * datos básicos de un producto (precio, stock, etc.).
     */
    public function update(User $user, Producto $producto): bool
    {
        return true;
    }

    /**
     * Solo un administrador puede eliminar un producto.
     */
    public function delete(User $user, Producto $producto): bool
    {
        return $user->isAdmin();
    }
}