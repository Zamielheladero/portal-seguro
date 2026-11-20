@extends('layouts.app')

@section('title', 'Cotizaciones')

@section('content')
    <h1>Cotizaciones recibidas</h1>

    @forelse ($cotizaciones as $cotizacion)
        <div class="fila-panel">
            <div>
                <h2><a href="{{ route('cotizaciones.show', $cotizacion) }}">{{ $cotizacion->codigo }}</a></h2>
                <p>{{ $cotizacion->nombre_cliente }} · {{ $cotizacion->municipio }}, {{ $cotizacion->departamento }}</p>
                <p>{{ $cotizacion->items_count }} {{ $cotizacion->items_count === 1 ? 'producto' : 'productos' }} · {{ $cotizacion->created_at?->format('d/m/Y H:i') }}</p>
            </div>
            <div class="fila-panel-lado">
                <strong>@cop($cotizacion->total)</strong>
                <span class="estado estado-{{ $cotizacion->estado }}">{{ ucfirst($cotizacion->estado) }}</span>
            </div>
        </div>
    @empty
        <div>
            <p>Todavía no han llegado cotizaciones.</p>
        </div>
    @endforelse

    {{ $cotizaciones->links('partials.paginacion') }}
@endsection
