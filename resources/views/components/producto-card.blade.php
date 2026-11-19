@props(['producto'])

<article class="product-card">
    <a class="visual-button" href="{{ route('catalogo.show', $producto) }}" aria-label="Ver {{ $producto->nombre }}">
        <x-producto-visual :producto="$producto" />
        @if ($producto->etiqueta)
            <span class="badge badge-light">{{ $producto->etiqueta }}</span>
        @endif
    </a>
    <div class="product-body">
        <p class="eyebrow">{{ $producto->etiquetaDe('uso') ?? $producto->etiquetaDe('categoria') ?? 'Producto' }}</p>
        <h3><a href="{{ route('catalogo.show', $producto) }}">{{ $producto->nombre }}</a></h3>
        <p class="muted">{{ $producto->resumen() ?: \Illuminate\Support\Str::limit($producto->descripcion, 70) }}</p>
        <div class="price">
            <strong>@cop($producto->precio)</strong>
            <span>por unidad</span>
        </div>
        <form method="POST" action="{{ route('cotizacion.agregar') }}">
            @csrf
            <input type="hidden" name="producto_id" value="{{ $producto->id }}">
            <button type="submit" class="btn btn-secondary">
                <x-icon name="plus" :size="20" />
                <span>Agregar a cotización</span>
            </button>
        </form>
    </div>
</article>
