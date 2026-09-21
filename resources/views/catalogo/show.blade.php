@extends('layouts.app')

@section('title', $producto->nombre)

@section('content')
    <p><a href="{{ route('catalogo.index') }}">&larr; Volver al catálogo</a></p>

    <h1>{{ $producto->nombre }}</h1>

    @if ($producto->imagen_url)
        <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}" width="300">
    @endif

    <p>{{ $producto->descripcion }}</p>

    <p><strong>Precio:</strong> ${{ number_format($producto->precio, 2) }}</p>
    <p><strong>Disponibles:</strong> {{ $producto->stock }}</p>
@endsection