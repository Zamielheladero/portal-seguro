@extends('layouts.app')

@section('title', 'Nueva publicación')

@section('content')
    <h1>Nueva publicación</h1>

    <form method="POST" action="{{ route('posts.store') }}">
        @csrf

        <label>Título</label>
        <input name="title" value="{{ old('title') }}" required maxlength="150">
        @error('title') <p>{{ $message }}</p> @enderror

        <label>Contenido</label>
        <textarea name="body" required maxlength="10000">{{ old('body') }}</textarea>
        @error('body') <p>{{ $message }}</p> @enderror

        <button type="submit">Publicar</button>
    </form>
@endsection