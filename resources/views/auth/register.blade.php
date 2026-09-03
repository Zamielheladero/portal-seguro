@extends('layouts.app')

@section('title', 'Registro')

@section('content')
    <h1>Crear cuenta</h1>

    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <label>Nombre</label>
        <input name="name" value="{{ old('name') }}" required maxlength="100">
        @error('name') <p>{{ $message }}</p> @enderror

        <label>Correo</label>
        <input type="email" name="email" value="{{ old('email') }}" required>
        @error('email') <p>{{ $message }}</p> @enderror

        <label>Contraseña</label>
        <input type="password" name="password" required>
        @error('password') <p>{{ $message }}</p> @enderror

        <label>Confirmar contraseña</label>
        <input type="password" name="password_confirmation" required>

        <button type="submit">Registrarme</button>
    </form>
@endsection