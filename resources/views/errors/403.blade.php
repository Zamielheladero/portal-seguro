@extends('errors.layout')

@section('code', '403')
@section('title', 'Acceso no permitido')
@section('message', $exception->getMessage() ?: 'No tienes permiso para acceder a esta sección.')
