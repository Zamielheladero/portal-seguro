{{-- resources/views/catalogo/show.blade.php --}}
{{-- Ajusta ->categoria, ->descripcion, ->stock si no existen en tu modelo. --}}
@extends('layouts.app')

@section('title', $producto->nombre)

@section('content')
@vite('resources/css/catalogo-detalle.css')

<a href="{{ route('catalogo.index') }}" class="detalle-volver">&larr; Volver al catálogo</a>

<div class="detalle-wrap">
    @if ($producto->imagen_url)
        <div class="detalle-imagen">
            <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}">
        </div>
    @else
        <div class="detalle-imagen sin-imagen">Sin imagen disponible</div>
    @endif

    <div class="detalle-info">
        @if(isset($producto->categoria))
            <span class="categoria">{{ $producto->categoria }}</span>
        @endif

        <h1>{{ $producto->nombre }}</h1>

        @if(isset($producto->stock))
            <span class="detalle-etiqueta-stock {{ $producto->stock > 0 ? '' : 'sin-stock' }}">
                {{ $producto->stock > 0 ? 'Disponible: '.$producto->stock.' un.' : 'Agotado' }}
            </span>
        @endif

        @if(isset($producto->descripcion))
            <p class="descripcion">{{ $producto->descripcion }}</p>
        @endif

        <div class="detalle-precio-box">
            <span class="detalle-precio">${{ number_format($producto->precio, 2) }}</span>

            <div class="detalle-acciones">
                @can('update', $producto)
                    <a href="{{ route('productos.edit', $producto) }}" class="btn-secundario">Editar</a>
                @endcan
                <a href="{{ route('catalogo.index') }}" class="btn-primario">Ver más productos</a>
            </div>
        </div>
    </div>
</div>
@endsection