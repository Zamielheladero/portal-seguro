@extends('layouts.app')

@section('title', 'Mi cotización')
@section('pagina_completa', '1')

@section('content')
@php
    $whatsappText = "Hola AgroPoste, quiero cotizar:\n"
        .$items->map(fn ($item) => "- {$item['cantidad']} × {$item['producto']->nombre}")->implode("\n")
        ."\nTotal estimado: $ ".number_format($totales['total'], 0, ',', '.');
@endphp
    <main class="quote-page page-bg">
        <div class="container">
            <span class="breadcrumb"><a href="{{ route('home') }}">Inicio</a> / Mi cotización</span>

            <div class="quote-title">
                <div>
                    <h1>Arme su cotización</h1>
                    <p>Le tomará menos de 3 minutos.</p>
                </div>
                <div class="stepper">
                    @foreach (['Productos', 'Ubicación', 'Resumen'] as $index => $label)
                        <div @class(['active' => $paso >= $index + 1])>
                            <b>
                                @if ($paso > $index + 1)
                                    <x-icon name="check" :size="16" />
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </b>
                            <span>{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="quote-layout">
                <section class="quote-main">
                    @if ($paso === 1)
                        <div class="panel">
                            <div class="panel-head">
                                <div>
                                    <span>Paso 1 de 3</span>
                                    <h2>Productos y cantidades</h2>
                                </div>
                                <a href="{{ route('catalogo.index') }}">+ Agregar producto</a>
                            </div>

                            @forelse ($items as $item)
                                <div class="quote-item">
                                    <x-producto-visual :producto="$item['producto']" />
                                    <div>
                                        <h3><a href="{{ route('catalogo.show', $item['producto']) }}">{{ $item['producto']->nombre }}</a></h3>
                                        <p>{{ $item['producto']->resumen() }}</p>
                                        <strong>@cop($item['producto']->precio) <small>/ unidad</small></strong>
                                    </div>
                                    <form method="POST" action="{{ route('cotizacion.actualizar', $item['producto']) }}" class="qty">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" name="cantidad" value="{{ max(0, $item['cantidad'] - 10) }}" aria-label="Quitar 10 unidades">
                                            <x-icon name="minus" :size="16" />
                                        </button>
                                        <b>{{ $item['cantidad'] }}</b>
                                        <button type="submit" name="cantidad" value="{{ $item['cantidad'] + 10 }}" aria-label="Agregar 10 unidades">
                                            <x-icon name="plus" :size="16" />
                                        </button>
                                    </form>
                                    <strong>@cop($item['total'])</strong>
                                </div>
                            @empty
                                <div class="quote-empty">
                                    <p>Todavía no ha agregado productos a su cotización.</p>
                                    <a href="{{ route('catalogo.index') }}" class="btn btn-secondary"><span>Ir al catálogo</span></a>
                                </div>
                            @endforelse
                        </div>

                        <div class="helper-box">
                            <div class="helper-title">
                                <span><x-icon name="calculator" /></span>
                                <div>
                                    <h3>¿No sabe cuántos postes necesita?</h3>
                                    <p>Calcule una cantidad aproximada para su cerca.</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('cotizacion.calcular') }}" class="helper-inputs">
                                @csrf
                                <label>
                                    Metros de cerca
                                    <input name="metros" value="{{ old('metros') }}" placeholder="Ej. 500" inputmode="numeric" required>
                                    @error('metros') <em class="field-error">{{ $message }}</em> @enderror
                                </label>
                                <label>
                                    Distancia entre postes
                                    <select name="distancia">
                                        @foreach (['2.5' => '2,5 metros', '3' => '3 metros', '2' => '2 metros'] as $value => $label)
                                            <option value="{{ $value }}" @selected(old('distancia', '2.5') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </label>
                                <button type="submit" class="btn btn-secondary"><span>Calcular cantidad</span></button>
                            </form>
                        </div>

                        @if ($items->isNotEmpty())
                            <div class="step-actions">
                                <a href="{{ route('cotizacion.show', ['paso' => 2]) }}" class="btn btn-primary">
                                    <x-icon name="arrow" :size="20" /><span>Continuar</span>
                                </a>
                            </div>
                        @endif
                    @elseif ($paso === 2)
                        <form method="POST" action="{{ route('cotizacion.entrega') }}" class="panel location-panel" data-form-entrega>
                            @csrf
                            <span>Paso 2 de 3</span>
                            <h2>¿Dónde queda su finca?</h2>
                            <p>Con esto calculamos el transporte y tiempo de entrega.</p>

                            <div class="field-row">
                                <label>
                                    Su nombre
                                    <input name="nombre_cliente" value="{{ old('nombre_cliente', $entrega['nombre_cliente'] ?? auth()->user()?->name) }}" autocomplete="name" maxlength="80" required>
                                    @error('nombre_cliente') <em class="field-error">{{ $message }}</em> @enderror
                                </label>
                                <label>
                                    Celular o WhatsApp
                                    <input name="telefono" type="tel" value="{{ old('telefono', $entrega['telefono'] ?? '') }}" autocomplete="tel" placeholder="Ej. 310 555 1234" maxlength="20" required>
                                    @error('telefono') <em class="field-error">{{ $message }}</em> @enderror
                                </label>
                            </div>
                            <label>
                                Departamento
                                <select name="departamento" data-departamento>
                                    @foreach (array_keys($cobertura) as $departamento)
                                        <option @selected(old('departamento', $entrega['departamento'] ?? 'Cundinamarca') === $departamento)>{{ $departamento }}</option>
                                    @endforeach
                                </select>
                                @error('departamento') <em class="field-error">{{ $message }}</em> @enderror
                            </label>
                            <label>
                                Municipio
                                <select name="municipio" data-municipio>
                                    @foreach ($cobertura as $departamento => $municipios)
                                        <optgroup label="{{ $departamento }}">
                                            @foreach ($municipios as $municipio)
                                                <option @selected(old('municipio', $entrega['municipio'] ?? 'Suesca') === $municipio)>{{ $municipio }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                @error('municipio') <em class="field-error">{{ $message }}</em> @enderror
                            </label>
                            <label class="note">
                                Indicaciones para llegar (opcional)
                                <textarea name="indicaciones" maxlength="500" placeholder="Ej. Vereda Hato Grande, 2 km después de la escuela">{{ old('indicaciones', $entrega['indicaciones'] ?? '') }}</textarea>
                                @error('indicaciones') <em class="field-error">{{ $message }}</em> @enderror
                            </label>
                            <div class="info-note">
                                <x-icon name="truck" /> Entrega estimada en 3 a 5 días hábiles.
                            </div>

                            <div class="step-actions">
                                <a href="{{ route('cotizacion.show') }}" class="btn btn-ghost"><span>Volver</span></a>
                                <button type="submit" class="btn btn-primary">
                                    <x-icon name="arrow" :size="20" /><span>Continuar</span>
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="panel final-review">
                            <span>Paso 3 de 3</span>
                            <h2>Revise su información</h2>
                            <div class="review-section">
                                <h3>Productos</h3>
                                @foreach ($items as $item)
                                    <p>
                                        <span>{{ $item['cantidad'] }} × {{ $item['producto']->nombre }}</span>
                                        <strong>@cop($item['total'])</strong>
                                    </p>
                                @endforeach
                            </div>
                            <div class="review-section">
                                <h3>Entrega</h3>
                                <p>
                                    <span>
                                        {{ $entrega['municipio'] }}, {{ $entrega['departamento'] }}
                                        <br>
                                        <small>{{ $entrega['nombre_cliente'] }} · {{ $entrega['telefono'] }}</small>
                                        <br>
                                        <small>Tiempo estimado: 3 a 5 días</small>
                                    </span>
                                    <a href="{{ route('cotizacion.show', ['paso' => 2]) }}">Editar</a>
                                </p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('cotizacion.store') }}" class="step-actions">
                            @csrf
                            <a href="{{ route('cotizacion.show', ['paso' => 2]) }}" class="btn btn-ghost"><span>Volver</span></a>
                            <button type="submit" class="btn btn-primary">
                                <x-icon name="arrow" :size="20" /><span>Guardar cotización</span>
                            </button>
                        </form>
                    @endif
                </section>

                <aside class="summary">
                    <h2>Resumen</h2>
                    <div class="summary-lines">
                        <p>
                            <span>Subtotal</span>
                            <b>@cop($totales['subtotal'])</b>
                        </p>
                        <p class="saving">
                            <span>Descuento por volumen</span>
                            <b>− @cop($totales['descuento'])</b>
                        </p>
                        <p>
                            <span>
                                Transporte estimado
                                <button type="button" title="Costo aproximado; un asesor lo confirma según el acceso a su finca." aria-label="Costo aproximado">?</button>
                            </span>
                            <b>@cop($totales['transporte'])</b>
                        </p>
                    </div>
                    <div class="total">
                        <span>Total estimado<small>IVA incluido</small></span>
                        <strong>@cop($totales['total'])</strong>
                    </div>

                    @if ($items->isNotEmpty())
                        <form method="POST" action="{{ route('cotizacion.store') }}">
                            @csrf
                            <button type="submit" class="btn btn-amber"><span>Solicitar cotización formal</span></button>
                        </form>
                        <a href="https://wa.me/{{ config('agroposte.whatsapp') }}?text={{ rawurlencode($whatsappText) }}" class="btn btn-whatsapp" target="_blank" rel="noopener noreferrer">
                            <x-icon name="whatsapp" :size="20" /><span>Enviar por WhatsApp</span>
                        </a>
                        <button type="button" class="download" data-print>
                            <x-icon name="download" :size="18" /> Descargar resumen en PDF
                        </button>
                    @endif

                    <div class="safe-note">
                        <x-icon name="shield" />
                        <span><b>Compra protegida</b>Un asesor confirmará precio y disponibilidad.</span>
                    </div>
                </aside>
            </div>
        </div>
    </main>
@endsection
