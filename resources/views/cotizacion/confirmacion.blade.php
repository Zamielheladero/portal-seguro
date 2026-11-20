@extends('layouts.app')

@section('title', 'Cotización enviada')
@section('pagina_completa', '1')

@section('content')
@php
    $asesora = config('agroposte.advisor');
    $whatsappText = "Hola {$asesora['name']}, le escribo por la cotización {$cotizacion->codigo}.";
@endphp
    <main class="confirmation page-bg">
        <div class="container">
            <div class="confirm-card">
                <span class="success-icon"><x-icon name="check" :size="36" /></span>
                <span class="overline">Solicitud recibida</span>
                <h1>¡Listo, {{ $cotizacion->primerNombre() }}!</h1>
                <p>Su cotización fue enviada. Un asesor la revisará y se comunicará con usted.</p>

                <div class="quote-number">
                    <span>Número de cotización</span>
                    <strong>{{ $cotizacion->codigo }}</strong>
                    <small>Válida por {{ config('agroposte.quote_valid_days') }} días</small>
                </div>

                <div class="print-only quote-detail">
                    <p>{{ $cotizacion->nombre_cliente }} · {{ $cotizacion->telefono }} · {{ $cotizacion->recogeEnPunto() ? 'Recoge en '.config('agroposte.pickup') : collect([$cotizacion->vereda, $cotizacion->municipio])->filter()->implode(', ') }}</p>
                    <table>
                        @foreach ($cotizacion->items as $item)
                            <tr>
                                <td>{{ $item->cantidad }} × {{ $item->nombre_producto }}</td>
                                <td>@cop($item->total_linea)</td>
                            </tr>
                        @endforeach
                        <tr><td>Descuento por volumen</td><td>− @cop($cotizacion->descuento)</td></tr>
                        <tr><td>{{ $cotizacion->recogeEnPunto() ? 'Transporte (lo recoge usted)' : 'Transporte estimado' }}</td><td>@cop($cotizacion->transporte)</td></tr>
                        <tr><th>Total estimado (IVA incluido)</th><th>@cop($cotizacion->total)</th></tr>
                    </table>
                </div>

                <div class="next">
                    <h2>¿Qué sigue?</h2>
                    @foreach ([
                        ['1', 'Revisamos disponibilidad', 'Hoy, antes de las 5:00 p. m.'],
                        $cotizacion->recogeEnPunto()
                            ? ['2', 'Alistamos su pedido', 'Le avisamos por WhatsApp cuándo puede recogerlo.']
                            : ['2', 'Confirmamos transporte', 'Validamos acceso y fecha de entrega.'],
                        ['3', 'Usted decide', 'Solo paga cuando apruebe la cotización.'],
                    ] as [$numero, $titulo, $descripcion])
                        <div>
                            <b>{{ $numero }}</b>
                            <span>
                                <strong>{{ $titulo }}</strong>
                                <small>{{ $descripcion }}</small>
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="advisor">
                    <span>{{ $asesora['initials'] }}</span>
                    <div>
                        <small>Su asesora</small>
                        <strong>{{ $asesora['name'] }}</strong>
                        <small>{{ $asesora['role'] }}</small>
                    </div>
                    <a href="https://wa.me/{{ config('agroposte.whatsapp') }}?text={{ rawurlencode($whatsappText) }}" class="btn btn-whatsapp" target="_blank" rel="noopener noreferrer">
                        <x-icon name="whatsapp" :size="20" /><span>Escribirle</span>
                    </a>
                </div>

                <div class="confirm-actions">
                    <button type="button" class="btn btn-secondary" data-print>
                        <x-icon name="download" :size="20" /><span>Descargar cotización</span>
                    </button>
                    <a href="{{ route('home') }}" class="btn btn-ghost"><span>Volver al inicio</span></a>
                </div>
            </div>
        </div>
    </main>
@endsection
