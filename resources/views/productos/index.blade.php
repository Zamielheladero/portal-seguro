@extends('layouts.app')

@section('title', 'Productos')

@section('content')
    <h1>Productos</h1>

    @if (session('status'))
        <p>{{ session('status') }}</p>
    @endif

    <a href="{{ route('productos.create') }}">Nuevo producto</a>

    @foreach ($productos as $producto)
        <div>
            @if ($producto->imagen_url)
                <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}" width="80">
            @endif

            <h2>{{ $producto->nombre }}</h2>
            <p>Precio: ${{ number_format($producto->precio, 2) }}</p>
            <p>Stock: {{ $producto->stock }}</p>
            <p>Estado: {{ $producto->activo ? 'Activo' : 'Inactivo' }}</p>

            <a href="{{ route('productos.edit', $producto) }}">Editar</a>

            @if (auth()->user()->isAdmin())
                <form method="POST" action="{{ route('productos.destroy', $producto) }}" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('¿Eliminar este producto?')">Eliminar</button>
                </form>
            @endif
        </div>
    @endforeach

    {{ $productos->links() }}
@endsection
