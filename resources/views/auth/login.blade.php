@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
    <h1>Iniciar sesión</h1>

    <form method="POST" action="{{ route('login.store') }}">
       @csrf

        <label>Correo</label>
        <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
        @error('email') <p>{{ $message }}</p> @enderror

        <label>Contraseña</label>
        <input type="password" name="password" autocomplete="current-password" required>

        <button type="submit">Entrar</button>
    </form>

    <p class="enlaces-cuenta">
        ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate</a>
        <span aria-hidden="true">·</span>
        <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
    </p>
@endsection