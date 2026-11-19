@extends('layouts.app')

@section('title', 'Cotización '.$cotizacion->codigo)

@section('content')
    <h1>Cotización {{ $cotizacion->codigo }}</h1>

    @if (session('status'))
        <p>{{ session('status') }}</p>
    @endif

    <div>
        <h2>Cliente</h2>
        <p>{{ $cotizacion->nombre_cliente }} · {{ $cotizacion->telefono }}</p>
        <p>{{ $cotizacion->municipio }}, {{ $cotizacion->departamento }}</p>
        @if ($cotizacion->indicaciones)
            <p>Indicaciones: {{ $cotizacion->indicaciones }}</p>
        @endif
        <p>
            Recibida el {{ $cotizacion->created_at?->format('d/m/Y H:i') }} · válida hasta {{ $cotizacion->valida_hasta->format('d/m/Y') }}
            @if ($cotizacion->user)
                · cuenta: {{ $cotizacion->user->email }}
            @endif
        </p>
    </div>

    <div>
        <h2>Productos</h2>
        <table class="tabla-panel">
            <thead>
                <tr><th>Producto</th><th>Cantidad</th><th>Precio unidad</th><th>Total</th></tr>
            </thead>
            <tbody>
                @foreach ($cotizacion->items as $item)
                    <tr>
                        <td>{{ $item->nombre_producto }}</td>
                        <td>{{ $item->cantidad }}</td>
                        <td>@cop($item->precio_unitario)</td>
                        <td>@cop($item->total_linea)</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr><td colspan="3">Subtotal</td><td>@cop($cotizacion->subtotal)</td></tr>
                <tr><td colspan="3">Descuento por volumen</td><td>− @cop($cotizacion->descuento)</td></tr>
                <tr><td colspan="3">Transporte estimado</td><td>@cop($cotizacion->transporte)</td></tr>
                <tr><th colspan="3">Total</th><th>@cop($cotizacion->total)</th></tr>
            </tfoot>
        </table>
    </div>

    <form method="POST" action="{{ route('cotizaciones.update', $cotizacion) }}">
        @csrf
        @method('PATCH')
        <label for="estado">Estado</label>
        <select id="estado" name="estado">
            @foreach (App\Http\Controllers\CotizacionPanelController::ESTADOS as $estado)
                <option value="{{ $estado }}" @selected($cotizacion->estado === $estado)>{{ ucfirst($estado) }}</option>
            @endforeach
        </select>
        @error('estado') <p>{{ $message }}</p> @enderror
        <button type="submit">Guardar estado</button>
    </form>

    <p><a href="{{ route('cotizaciones.index') }}">Volver a cotizaciones</a></p>
@endsection
