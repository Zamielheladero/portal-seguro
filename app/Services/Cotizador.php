<?php

namespace App\Services;

use App\Models\Producto;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Collection;

/**
 * Cotización en curso del visitante, guardada en su sesión.
 * Solo se guardan id => cantidad; precios y nombres siempre se leen
 * de la base de datos para que nadie pueda manipularlos desde el navegador.
 */
class Cotizador
{
    private const CLAVE_ITEMS = 'cotizacion.items';

    private const CLAVE_ENTREGA = 'cotizacion.entrega';

    /** @var Collection<int, array{producto: Producto, cantidad: int, total: int}>|null */
    private ?Collection $cache = null;

    public function __construct(private Session $session) {}

    public function agregar(Producto $producto, int $cantidad): void
    {
        $this->fijar($producto, $this->cantidadDe($producto) + $cantidad);
    }

    public function fijar(Producto $producto, int $cantidad): void
    {
        $items = $this->itemsCrudos();
        $cantidad = min($cantidad, (int) config('agroposte.max_quantity'));

        if ($cantidad <= 0) {
            unset($items[$producto->id]);
        } else {
            $items[$producto->id] = $cantidad;
        }

        $this->session->put(self::CLAVE_ITEMS, $items);
        $this->cache = null;
    }

    public function cantidadDe(Producto $producto): int
    {
        return $this->itemsCrudos()[$producto->id] ?? 0;
    }

    public function conteo(): int
    {
        return count($this->itemsCrudos());
    }

    public function vacia(): bool
    {
        return $this->items()->isEmpty();
    }

    /**
     * Productos de la cotización que siguen disponibles en el catálogo.
     *
     * @return Collection<int, array{producto: Producto, cantidad: int, total: int}>
     */
    public function items(): Collection
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        $cantidades = $this->itemsCrudos();

        return $this->cache = Producto::disponibles()
            ->whereKey(array_keys($cantidades))
            ->orderBy('nombre')
            ->get()
            ->map(fn (Producto $producto) => [
                'producto' => $producto,
                'cantidad' => $cantidades[$producto->id],
                'total' => $producto->precioEntero() * $cantidades[$producto->id],
            ])
            ->values();
    }

    /**
     * @return array{subtotal: int, descuento: int, transporte: int, total: int}
     */
    public function totales(): array
    {
        $subtotal = (int) $this->items()->sum('total');
        $regla = config('agroposte.volume_discount');
        $descuento = $subtotal >= $regla['from_subtotal'] ? (int) round($subtotal * $regla['percent'] / 100) : 0;
        $transporte = $subtotal > 0 ? (int) config('agroposte.shipping_estimate') : 0;

        return [
            'subtotal' => $subtotal,
            'descuento' => $descuento,
            'transporte' => $transporte,
            'total' => $subtotal - $descuento + $transporte,
        ];
    }

    /**
     * @param  array<string, string|null>  $entrega
     */
    public function guardarEntrega(array $entrega): void
    {
        $this->session->put(self::CLAVE_ENTREGA, $entrega);
    }

    /**
     * @return array<string, string|null>|null
     */
    public function entrega(): ?array
    {
        return $this->session->get(self::CLAVE_ENTREGA);
    }

    public function vaciar(): void
    {
        $this->session->forget([self::CLAVE_ITEMS, self::CLAVE_ENTREGA]);
        $this->cache = null;
    }

    /**
     * @return array<int, int>
     */
    private function itemsCrudos(): array
    {
        return $this->session->get(self::CLAVE_ITEMS, []);
    }
}
