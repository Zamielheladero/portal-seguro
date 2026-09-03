@extends('layouts.app')

@section('title', $post->title)

@section('content')
    <h1>{{ $post->title }}</h1>
    <p>Por: {{ $post->user->name }}</p>
    <p>{{ $post->body }}</p>

    @auth
        @if (auth()->id() === $post->user_id)
            <a href="{{ route('posts.edit', $post) }}">Editar</a>

            <form method="POST" action="{{ route('posts.destroy', $post) }}">
                @csrf
                @method('DELETE')
                <button type="submit">Eliminar</button>
            </form>
        @endif
    @endauth

    <a href="{{ route('posts.index') }}">Volver</a>
@endsection