{{-- resources/views/catalogo/show.blade.php --}}
{{-- Ajusta ->categoria, ->descripcion, ->stock si no existen en tu modelo. --}}
@extends('layouts.app')

@section('title', $producto->nombre)

@section('content')
<style>
    .detalle-volver {
        display: inline-block;
        margin-bottom: 1.25rem;
        color: #8A8378;
        text-decoration: none;
        font-size: .85rem;
    }
    .detalle-volver:hover { color: #1F2A33; }

    .detalle-wrap {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2.5rem;
        border: 1px solid #E4E0D8;
        background: #FAF8F5;
        padding: 2rem;
    }
    @media (max-width: 720px) {
        .detalle-wrap { grid-template-columns: 1fr; }
    }

    .detalle-imagen, .detalle-imagen img {
        width: 100%;
        min-height: 260px;
        object-fit: cover;
        display: block;
    }
    .detalle-imagen.sin-imagen {
        background: #EFEBE3;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #B7AF9E;
        font-size: .85rem;
    }

    .detalle-info .categoria {
        font-size: .78rem;
        color: #8A8378;
        margin-bottom: .35rem;
        display: block;
    }
    .detalle-info h1 {
        margin: 0 0 .75rem 0;
        font-size: 1.6rem;
        font-weight: 800;
        color: #1F2A33;
    }
    .detalle-info .descripcion {
        color: #4B4740;
        line-height: 1.55;
        margin-bottom: 1.5rem;
        max-width: 46ch;
    }

    .detalle-etiqueta-stock {
        display: inline-block;
        padding: .25rem .7rem;
        font-size: .75rem;
        font-weight: 700;
        color: #fff;
        background: #4B6B4B;
        margin-bottom: 1.25rem;
    }
    .detalle-etiqueta-stock.sin-stock { background: #9C3B2E; }

    .detalle-precio-box {
        border-top: 1px solid #E4E0D8;
        padding-top: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .detalle-precio {
        font-size: 1.9rem;
        font-weight: 800;
        color: #C1622A;
    }

    .detalle-acciones {
        display: flex;
        gap: .75rem;
    }
    .btn-primario, .btn-secundario {
        padding: .65rem 1.1rem;
        font-size: .88rem;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }
    .btn-primario {
        background: #1F2A33;
        color: #fff;
    }
    .btn-primario:hover { background: #C1622A; }
    .btn-secundario {
        background: transparent;
        color: #1F2A33;
        border: 1px solid #D8D3CB;
    }
</style>

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
                @auth
                    <a href="{{ route('productos.edit', $producto) }}" class="btn-secundario">Editar</a>
                @endauth
                <a href="{{ route('catalogo.index') }}" class="btn-primario">Ver más productos</a>
            </div>
        </div>
    </div>
</div>
@endsection