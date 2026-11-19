@php
    $content = [
        'transporte' => ['Llegamos hasta su finca', 'Transporte claro, incluso en zona rural', 'Consulte cobertura, costo estimado y tiempos antes de pedir.', 'truck', 'Consulte su municipio', 'Transporte'],
        'garantias' => ['Compra respaldada', 'Madera que dura, garantía que responde', 'Le explicamos qué cubre cada garantía, sin letra pequeña.', 'shield', 'Protección para cada producto', 'Garantías'],
        'asesoria' => ['Asesoría técnica', '¿Qué estacón necesita?', 'Responda dos preguntas sencillas y le recomendamos la mejor opción.', 'phone', 'Cuéntenos sobre su proyecto', 'Asesoría'],
    ][$tipo];
    [$eyebrow, $heading, $intro, $icon, $panelTitle, $pageTitle] = $content;
@endphp

@extends('layouts.app')

@section('title', $pageTitle)
@section('pagina_completa', '1')

@section('content')
    <main class="info-page page-bg">
        <section class="info-hero">
            <div class="container">
                <span class="overline">{{ $eyebrow }}</span>
                <h1>{{ $heading }}</h1>
                <p>{{ $intro }}</p>
            </div>
        </section>

        <div class="container info-grid">
            <section class="panel info-main">
                <span class="big-icon"><x-icon :name="$icon" :size="36" /></span>
                <h2>{{ $panelTitle }}</h2>

                @if ($tipo === 'transporte')
                    <form method="GET" action="{{ route('transporte') }}" class="search-box">
                        <x-icon name="search" />
                        <input name="municipio" value="{{ $resultado['busqueda'] ?? '' }}" maxlength="60" aria-label="Municipio" placeholder="Ej. Suesca, Chocontá, Ubaté...">
                    </form>

                    @if ($resultado)
                        @if ($resultado['municipio'])
                            <div class="success-note">
                                <x-icon name="check" /> Sí llegamos a {{ $resultado['municipio'] }}, {{ $resultado['departamento'] }}. Transporte estimado desde @cop(config('agroposte.shipping_estimate')).
                            </div>
                        @else
                            <div class="info-note">
                                <x-icon name="truck" /> No tenemos “{{ $resultado['busqueda'] }}” en la lista, pero escríbanos y revisamos si podemos llegar.
                            </div>
                        @endif
                    @endif

                    <div class="coverage">
                        <x-icon name="map" :size="64" />
                        <div>
                            <strong>Cundinamarca y Boyacá</strong>
                            <p>Cobertura activa en {{ $totalMunicipios }} municipios. Entregas de 2 a 7 días.</p>
                        </div>
                    </div>
                @elseif ($tipo === 'garantias')
                    <div class="guarantee-list">
                        @foreach ([
                            ['shield', 'Hasta 10 años', 'Madera inmunizada contra hongos y plagas.'],
                            ['check', 'Calidad verificada', 'Medidas y tratamiento revisados antes de despacho.'],
                            ['leaf', 'Origen responsable', 'Madera de plantaciones legales y seleccionadas.'],
                        ] as [$iconoItem, $titulo, $descripcion])
                            <div>
                                <x-icon :name="$iconoItem" />
                                <span>
                                    <strong>{{ $titulo }}</strong>
                                    <p>{{ $descripcion }}</p>
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <form method="GET" action="{{ route('asesoria') }}" class="quiz">
                        <fieldset>
                            <legend>¿Para qué lo va a usar?</legend>
                            <div>
                                @foreach (['potrero' => 'Cerca de potrero', 'corral' => 'Corral', 'construccion' => 'Construcción'] as $value => $label)
                                    <label class="choice">
                                        <input type="radio" name="uso" value="{{ $value }}" @checked($uso === $value) required>
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>¿Cómo es el suelo?</legend>
                            <div>
                                @foreach (['humedo' => 'Húmedo', 'seco' => 'Seco', 'nose' => 'No sé'] as $value => $label)
                                    <label class="choice">
                                        <input type="radio" name="suelo" value="{{ $value }}" @checked($suelo === $value) required>
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>
                        <button type="submit" class="btn btn-amber"><span>Ver mi recomendación</span></button>
                    </form>

                    @if ($recomendado)
                        <div class="recommendation">
                            <span class="overline">Le recomendamos</span>
                            <div class="recommendation-card">
                                <x-producto-visual :producto="$recomendado" />
                                <div>
                                    <h3>{{ $recomendado->nombre }}</h3>
                                    <p>{{ collect([$recomendado->resumen(), $recomendado->durabilidadTexto() ? 'dura '.$recomendado->durabilidadTexto() : null])->filter()->implode(' · ') }}</p>
                                    <a href="{{ route('catalogo.show', $recomendado) }}" class="btn btn-secondary">
                                        <span>Ver producto</span><x-icon name="arrow" :size="18" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    @elseif ($respondio)
                        <div class="info-note">
                            <x-icon name="phone" /> No tenemos un producto disponible para ese caso. Escríbale a un asesor y le ayudamos.
                        </div>
                    @endif
                @endif
            </section>

            <aside class="info-aside">
                <h3>¿Prefiere hablar con alguien?</h3>
                <p>Le respondemos en horario de lunes a sábado, 7 a. m. a 6 p. m.</p>
                <a href="https://wa.me/{{ config('agroposte.whatsapp') }}" class="btn btn-whatsapp" target="_blank" rel="noopener noreferrer">
                    <x-icon name="whatsapp" :size="20" /><span>Hablar por WhatsApp</span>
                </a>
                <small>Respuesta usual en menos de 10 minutos.</small>
            </aside>
        </div>
    </main>
@endsection
