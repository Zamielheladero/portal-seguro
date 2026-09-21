@extends('layouts.app')

@section('title', 'Catálogo')

@section('content')
    <h1>Catálogo de productos</h1>

    @forelse ($productos as $producto)
        <div>
            @if ($producto->imagen_url)
                <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}" width="120">
            @endif

            <h2><a href="{{ route('catalogo.show', $producto) }}">{{ $producto->nombre }}</a></h2>
            <p>${{ number_format($producto->precio, 2) }}</p>
        </div>
    @empty
        <p>No hay productos disponibles por el momento.</p>
    @endforelse

    {{ $productos->links() }}
@endsection