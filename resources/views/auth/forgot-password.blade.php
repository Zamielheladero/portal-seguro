@extends('layouts.app')

@section('title', 'Recuperar contraseña')

@section('content')
    <h1>Recuperar contraseña</h1>
    <p>Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña.</p>

    @if (session('status'))
        <p>{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <label>Correo</label>
        <input type="email" name="email" value="{{ old('email') }}" required>
        @error('email') <p>{{ $message }}</p> @enderror

        <button type="submit">Enviar enlace de recuperación</button>
    </form>

    <p><a href="{{ route('login') }}">Volver al inicio de sesión</a></p>
@endsection