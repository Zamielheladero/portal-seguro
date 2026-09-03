@extends('layouts.app')

@section('title', 'Editar publicación')

@section('content')
    <h1>Editar publicación</h1>

    <form method="POST" action="{{ route('posts.update', $post) }}">
        @csrf
        @method('PATCH')

        <label>Título</label>
        <input name="title" value="{{ old('title', $post->title) }}" required maxlength="150">
        @error('title') <p>{{ $message }}</p> @enderror

        <label>Contenido</label>
        <textarea name="body" required maxlength="10000">{{ old('body', $post->body) }}</textarea>
        @error('body') <p>{{ $message }}</p> @enderror

        <button type="submit">Guardar cambios</button>
    </form>
@endsection