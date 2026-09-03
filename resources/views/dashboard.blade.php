@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1>Dashboard</h1>
    <p>Bienvenido, {{ auth()->user()->name }}.</p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Cerrar sesión</button>
    </form>
@endsection