@extends('layouts.app')

@section('title', 'Catálogo')

@section('content')
@vite('resources/css/catalogo.css')

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