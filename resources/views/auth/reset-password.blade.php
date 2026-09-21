@extends('layouts.app')

@section('title', 'Restablecer contraseña')

@section('content')
    <h1>Restablecer contraseña</h1>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <label>Correo</label>
        <input type="email" name="email" value="{{ old('email', $email) }}" required>
        @error('email') <p>{{ $message }}</p> @enderror

        <label>Nueva contraseña</label>
        <input type="password" name="password" required>
        @error('password') <p>{{ $message }}</p> @enderror

        <label>Confirmar contraseña</label>
        <input type="password" name="password_confirmation" required>

        <button type="submit">Restablecer contraseña</button>
    </form>
@endsection