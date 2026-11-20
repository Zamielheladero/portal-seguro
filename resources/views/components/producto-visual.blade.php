@props(['producto', 'grande' => false])

{{-- Foto del producto si la tiene; si no, la ilustración de madera/alambre del diseño. --}}
<div {{ $attributes->class(['product-visual', $producto->tono(), 'large' => $grande, 'con-foto' => $producto->imagen_url]) }}>
    @if ($producto->imagen_url)
        <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}" loading="lazy">
    @else
        <div class="wood-stack" aria-hidden="true">
            <i></i>
            <i></i>
            <i></i>
            <i></i>
        </div>
    @endif
    <span>
        <x-icon :name="$producto->esMetal() ? 'fence' : 'leaf'" :size="18" /> Producto de campo
    </span>
</div>
