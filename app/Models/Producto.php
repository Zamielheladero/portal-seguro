<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    /**
     * Filtros del catálogo (diseño AgroPoste): campo => [título, [valor => etiqueta]].
     * También sirven de lista blanca para validar el panel y la URL del catálogo.
     */
    public const FILTROS = [
        'categoria' => ['Tipo de producto', [
            'estacones' => 'Estacones y postes',
            'varetas' => 'Varetas',
            'tablas' => 'Tablas',
            'alambre' => 'Alambre y accesorios',
        ]],
        'tratamiento' => ['Tratamiento', [
            'inmunizado' => 'Inmunizado',
            'natural' => 'Madera natural',
            'galvanizado' => 'Galvanizado',
        ]],
        'uso' => ['Uso recomendado', [
            'cerca' => 'Cerca ganadera',
            'corral' => 'Corral',
            'construccion' => 'Construcción',
        ]],
        'largo' => ['Medidas', [
            'corto' => '2 a 2,5 metros',
            'largo' => '3 metros o más',
        ]],
    ];

    /**
     * Órdenes permitidos en el catálogo: valor => etiqueta.
     */
    public const ORDENES = [
        'recientes' => 'Más recientes',
        'menor-precio' => 'Menor precio',
        'mayor-duracion' => 'Mayor duración',
    ];

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria',
        'tratamiento',
        'uso',
        'material',
        'medida',
        'largo_m',
        'etiqueta',
        'durabilidad_min',
        'durabilidad_max',
        'precio',
        'stock',
        'imagen',
        'activo',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'stock' => 'integer',
        'activo' => 'boolean',
        'largo_m' => 'decimal:2',
        'durabilidad_min' => 'integer',
        'durabilidad_max' => 'integer',
    ];

    /**
     * URL pública de la imagen (o null si no tiene).
     * Uso en las vistas: $producto->imagen_url
     */
    public function getImagenUrlAttribute(): ?string
    {
        return $this->imagen ? asset('storage/'.$this->imagen) : null;
    }

    /**
     * Solo productos activos y con stock disponible (para el catálogo público).
     */
    public function scopeDisponibles($query)
    {
        return $query->where('activo', true)->where('stock', '>', 0);
    }

    /**
     * Precio en pesos enteros (la columna guarda dos decimales).
     */
    public function precioEntero(): int
    {
        return (int) round((float) $this->precio);
    }

    /**
     * Etiqueta legible de un campo con lista fija (categoria, tratamiento, uso).
     */
    public function etiquetaDe(string $campo): ?string
    {
        $valor = $this->getAttribute($campo);

        return self::FILTROS[$campo][1][$valor] ?? null;
    }

    /**
     * Color de la ilustración cuando el producto no tiene foto.
     */
    public function tono(): string
    {
        $material = mb_strtolower((string) $this->material.' '.$this->nombre);

        return match (true) {
            $this->categoria === 'alambre' => str_contains($material, 'grapa') ? 'grapa' : 'alambre',
            $this->categoria === 'tablas' => 'tabla',
            str_contains($material, 'guayac') => 'guayacan',
            str_contains($material, 'pino') => 'pino',
            default => 'eucalyptus',
        };
    }

    public function esMetal(): bool
    {
        return in_array($this->tono(), ['alambre', 'grapa'], true);
    }

    /**
     * Línea corta para las tarjetas: "Eucalipto inmunizado · 2,2 m × 10–12 cm".
     */
    public function resumen(): string
    {
        return collect([$this->material, $this->medida])->filter()->implode(' · ');
    }

    public function durabilidadTexto(): ?string
    {
        if (! $this->durabilidad_max) {
            return null;
        }

        return $this->durabilidad_min && $this->durabilidad_min !== $this->durabilidad_max
            ? "{$this->durabilidad_min}–{$this->durabilidad_max} años"
            : "{$this->durabilidad_max} años";
    }

    /**
     * Ancho de la barra de durabilidad en pasos de 10 % (clase CSS, sin style en línea por la CSP).
     */
    public function durabilidadPorcentaje(): int
    {
        return (int) min(100, round(($this->durabilidad_max ?? 0) / 20 * 10) * 10);
    }

    /**
     * Precio por unidad según la cantidad (tabla de volumen del diseño).
     *
     * @return list<array{rango: string, precio: int, ahorro: int}>
     */
    public function preciosPorVolumen(): array
    {
        $precio = $this->precioEntero();

        return [
            ['rango' => '50–99', 'precio' => $precio, 'ahorro' => 0],
            ['rango' => '100–499', 'precio' => (int) (round($precio * 0.92 / 100) * 100), 'ahorro' => 8],
            ['rango' => '500+', 'precio' => (int) (round($precio * 0.85 / 100) * 100), 'ahorro' => 15],
        ];
    }

    /**
     * Ficha técnica armada con los datos que tenga el producto.
     *
     * @return array<string, string>
     */
    public function fichaTecnica(): array
    {
        return array_filter([
            'Material' => $this->material,
            'Medidas' => $this->medida,
            'Tratamiento' => $this->etiquetaDe('tratamiento'),
            'Uso recomendado' => $this->etiquetaDe('uso'),
            'Tipo de producto' => $this->etiquetaDe('categoria'),
            'Disponibles' => $this->stock.' unidades',
        ]);
    }
}
