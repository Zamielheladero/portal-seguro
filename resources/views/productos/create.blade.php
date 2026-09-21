@extends('layouts.app')

@section('title', 'Nuevo producto')

@section('content')
    <h1>Nuevo producto</h1>

    <form method="POST" action="{{ route('productos.store') }}" enctype="multipart/form-data">
        @csrf

        <label>Nombre</label>
        <input type="text" name="nombre" value="{{ old('nombre') }}" required>
        @error('nombre') <p>{{ $message }}</p> @enderror

        <label>Descripción</label>
        <textarea name="descripcion">{{ old('descripcion') }}</textarea>
        @error('descripcion') <p>{{ $message }}</p> @enderror

        <label>Precio</label>
        <input type="number" name="precio" step="0.01" min="0" value="{{ old('precio') }}" required>
        @error('precio') <p>{{ $message }}</p> @enderror

        <label>Stock</label>
        <input type="number" name="stock" min="0" value="{{ old('stock', 0) }}" required>
        @error('stock') <p>{{ $message }}</p> @enderror

        <label>Imagen</label>
        <input type="file" name="imagen" accept="image/*">
        @error('imagen') <p>{{ $message }}</p> @enderror

        <label>
            <input type="checkbox" name="activo" value="1" checked>
            Producto activo (visible en catálogo)
        </label>

        <button type="submit">Guardar</button>
    </form>

    <p><a href="{{ route('productos.index') }}">Volver al listado</a></p>
@endsection