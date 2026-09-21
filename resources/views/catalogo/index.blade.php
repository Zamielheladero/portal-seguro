@extends('layouts.app')

@section('title', 'Catálogo')

@section('content')
<style>
    .catalogo-hero {
        background: #1F2A33;
        color: #F2F0EC;
        padding: 3rem 2rem;
        border-radius: 4px;
        margin-bottom: 2.5rem;
    }
    .catalogo-hero h1 {
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: -0.01em;
        margin: 0;
        color: #FFFFFF !important;
    }

    .catalogo-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .producto-card {
        border: 1px solid #E4E0D8;
        background: #FAF8F5;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .producto-card img {
        width: 100%;
        height: 160px;
        object-fit: cover;
        display: block;
    }
    .producto-card .sin-imagen {
        width: 100%;
        height: 160px;
        background: #EFEBE3;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #B7AF9E;
        font-size: .8rem;
    }
    .producto-card .cuerpo {
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: .4rem;
        flex: 1;
    }
    .producto-card h2 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
    }
    .producto-card h2 a {
        color: #1F2A33;
        text-decoration: none;
    }
    .producto-card h2 a:hover {
        color: #C1622A;
    }
    .producto-card .precio {
        margin-top: auto;
        padding-top: .6rem;
        border-top: 1px solid #E4E0D8;
        font-size: 1.15rem;
        font-weight: 800;
        color: #C1622A;
    }

    .catalogo-vacio {
        color: #8A8378;
    }
</style>

<div class="catalogo-hero">
    <h1>Catálogo de productos</h1>
</div>

<div class="catalogo-grid">
    @forelse ($productos as $producto)
        <div class="producto-card">
            @if ($producto->imagen_url)
                <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}">
            @else
                <div class="sin-imagen">Sin imagen</div>
            @endif

            <div class="cuerpo">
                <h2><a href="{{ route('catalogo.show', $producto) }}">{{ $producto->nombre }}</a></h2>
                <p class="precio">${{ number_format($producto->precio, 2) }}</p>
            </div>
        </div>
    @empty
        <p class="catalogo-vacio">No hay productos disponibles por el momento.</p>
    @endforelse
</div>

{{ $productos->links() }}
@endsection