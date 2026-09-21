<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'stock',
        'imagen',
        'activo',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'stock' => 'integer',
        'activo' => 'boolean',
    ];

    /**
     * URL pública de la imagen (o null si no tiene).
     * Uso en las vistas: $producto->imagen_url
     */
    public function getImagenUrlAttribute(): ?string
    {
        return $this->imagen ? asset('storage/' . $this->imagen) : null;
    }

    /**
     * Solo productos activos y con stock disponible (para el catálogo público).
     */
    public function scopeDisponibles($query)
    {
        return $query->where('activo', true)->where('stock', '>', 0);
    }
}