<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cotizacion extends Model
{
    protected $table = 'cotizaciones';

    /**
     * 'codigo', 'user_id' y 'estado' no son asignables: los pone el sistema.
     */
    protected $fillable = [
        'nombre_cliente',
        'telefono',
        'tipo_entrega',
        'departamento',
        'municipio',
        'vereda',
        'indicaciones',
        'subtotal',
        'descuento',
        'transporte',
        'total',
        'valida_hasta',
    ];

    protected $casts = [
        'valida_hasta' => 'date',
        'subtotal' => 'integer',
        'descuento' => 'integer',
        'transporte' => 'integer',
        'total' => 'integer',
    ];

    protected static function booted(): void
    {
        static::created(function (Cotizacion $cotizacion): void {
            $cotizacion->codigo = sprintf('AGP-%s-%05d', $cotizacion->created_at?->format('Y'), $cotizacion->id);
            $cotizacion->saveQuietly();
        });
    }

    /**
     * @return HasMany<CotizacionItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(CotizacionItem::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public const DOMICILIO = 'domicilio';

    public const RECOGER = 'recoger';

    public function recogeEnPunto(): bool
    {
        return $this->tipo_entrega === self::RECOGER;
    }

    public function primerNombre(): string
    {
        return explode(' ', trim($this->nombre_cliente))[0];
    }
}
