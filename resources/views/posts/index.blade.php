@extends('layouts.app')

@section('title', 'Publicaciones')

@section('content')
    <h1>Publicaciones</h1>

    <a href="{{ route('posts.create') }}">Nueva publicación</a>

    @foreach ($posts as $post)
        <div>
            <h2><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a></h2>
            <p>Por: {{ $post->user->name }}</p>
        </div>
    @endforeach
@endsection