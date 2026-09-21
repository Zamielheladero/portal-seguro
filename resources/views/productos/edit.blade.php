@extends('layouts.app')

@section('title', 'Editar producto')

@section('content')
    <h1>Editar producto</h1>

    <form method="POST" action="{{ route('productos.update', $producto) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label>Nombre</label>
        <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre) }}" required>
        @error('nombre') <p>{{ $message }}</p> @enderror

        <label>Descripción</label>
        <textarea name="descripcion">{{ old('descripcion', $producto->descripcion) }}</textarea>
        @error('descripcion') <p>{{ $message }}</p> @enderror

        <label>Precio</label>
        <input type="number" name="precio" step="0.01" min="0" value="{{ old('precio', $producto->precio) }}" required>
        @error('precio') <p>{{ $message }}</p> @enderror

        <label>Stock</label>
        <input type="number" name="stock" min="0" value="{{ old('stock', $producto->stock) }}" required>
        @error('stock') <p>{{ $message }}</p> @enderror

        @if ($producto->imagen_url)
            <p>Imagen actual:</p>
            <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}" width="120">
        @endif

        <label>Reemplazar imagen (opcional)</label>
        <input type="file" name="imagen" accept="image/*">
        @error('imagen') <p>{{ $message }}</p> @enderror

        <label>
            <input type="checkbox" name="activo" value="1" {{ old('activo', $producto->activo) ? 'checked' : '' }}>
            Producto activo (visible en catálogo)
        </label>

        <button type="submit">Guardar cambios</button>
    </form>

    <p><a href="{{ route('productos.index') }}">Volver al listado</a></p>
@endsection