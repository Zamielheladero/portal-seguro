<?php

namespace App\Policies;

use App\Models\Producto;
use App\Models\User;

/**
 * SCRUM-28 (CMS-10) / SCRUM-42 (SEC-02).
 *
 * El panel de productos es solo para personal interno (admin o vendedor).
 * Un 'cliente' registrado desde el sitio público recibe 403 en todo el panel.
 * El catálogo público (/catalogo) no pasa por esta Policy.
 */
class ProductoPolicy
{
    /**
     * Ver el listado del panel (incluye productos inactivos o sin stock).
     */
    public function viewAny(User $user): bool
    {
        return $user->isStaff();
    }

    /**
     * Crear productos nuevos.
     */
    public function create(User $user): bool
    {
        return $user->isStaff();
    }

    /**
     * Admin y vendedor pueden editar datos de un producto (precio, stock, etc.).
     * Pendiente en SCRUM-28: limitar al vendedor a solo precio y stock.
     */
    public function update(User $user, Producto $producto): bool
    {
        return $user->isStaff();
    }

    /**
     * Solo un administrador puede eliminar un producto.
     */
    public function delete(User $user, Producto $producto): bool
    {
        return $user->isAdmin();
    }
}
