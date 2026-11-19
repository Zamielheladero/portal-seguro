@extends('layouts.app')

@section('title', $producto->nombre)
@section('pagina_completa', '1')

@section('content')
<main class="page-bg detail-page">
    <div class="container">
        <span class="breadcrumb">
            <a href="{{ route('home') }}">Inicio</a> /
            <a href="{{ route('catalogo.index') }}">Catálogo</a>
            @if ($producto->categoria)
                / <a href="{{ route('catalogo.index', ['categoria' => [$producto->categoria]]) }}">{{ $producto->etiquetaDe('categoria') }}</a>
            @endif
        </span>

        <div class="detail-grid">
            <div class="gallery">
                <x-producto-visual :producto="$producto" grande />
                @if ($relacionados->isNotEmpty())
                    <div class="thumbs">
                        <x-producto-visual :producto="$producto" />
                        @foreach ($relacionados as $relacionado)
                            <a href="{{ route('catalogo.show', $relacionado) }}" aria-label="Ver {{ $relacionado->nombre }}">
                                <x-producto-visual :producto="$relacionado" />
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="detail-info">
                <div class="badges">
                    @if ($producto->tratamiento)
                        <span class="badge badge-green">{{ $producto->etiquetaDe('tratamiento') }}</span>
                    @endif
                    @if ($producto->etiqueta && $producto->etiqueta !== $producto->etiquetaDe('tratamiento'))
                        <span class="badge badge-amber">{{ $producto->etiqueta }}</span>
                    @endif
                </div>
                <h1>{{ $producto->nombre }}</h1>
                @if ($producto->descripcion)
                    <p class="detail-sub">{{ $producto->descripcion }}</p>
                @endif
                <div class="rating">
                    {{ collect([$producto->resumen(), 'Disponible: '.$producto->stock.' un.'])->filter()->implode(' · ') }}
                </div>

                <div class="detail-price">
                    <strong>@cop($producto->precio)</strong>
                    <span>por unidad desde 50 unidades</span>
                </div>

                <div class="volume-table" role="table" aria-label="Precio por volumen">
                    <div role="row">
                        <span role="columnheader">Cantidad</span>
                        <span role="columnheader">Precio unidad</span>
                        <span role="columnheader">Ahorro</span>
                    </div>
                    @foreach ($producto->preciosPorVolumen() as $nivel)
                        <div role="row" @class(['recommended' => $loop->index === 1])>
                            <b role="cell">{{ $nivel['rango'] }}</b>
                            <span role="cell">@cop($nivel['precio'])</span>
                            <small role="cell">{{ $nivel['ahorro'] ? $nivel['ahorro'].'%' : '—' }}</small>
                        </div>
                    @endforeach
                </div>

                <form method="POST" action="{{ route('cotizacion.agregar') }}">
                    @csrf
                    <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                    <input type="hidden" name="ir_a_cotizacion" value="1">
                    <label class="qty-label" for="cantidad">¿Cuántos necesita?</label>
                    <div class="buy-row">
                        <div class="qty" data-qty data-step="10">
                            <button type="button" data-qty-minus aria-label="Quitar 10"><x-icon name="minus" :size="18" /></button>
                            <input id="cantidad" name="cantidad" type="number" inputmode="numeric" min="1" max="{{ config('agroposte.max_quantity') }}" value="{{ old('cantidad', 50) }}">
                            <button type="button" data-qty-plus aria-label="Agregar 10"><x-icon name="plus" :size="18" /></button>
                        </div>
                        <button type="submit" class="btn btn-amber">
                            <x-icon name="plus" :size="20" /><span>Agregar a cotización</span>
                        </button>
                    </div>
                    @error('cantidad')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </form>

                <div class="detail-links">
                    <a href="{{ route('asesoria') }}" class="advisor-link">
                        <x-icon name="phone" :size="19" /> ¿Tiene dudas? Pregunte a un asesor
                    </a>
                    @can('update', $producto)
                        <a href="{{ route('productos.edit', $producto) }}" class="advisor-link">Editar</a>
                    @endcan
                </div>
            </div>
        </div>

        <div class="spec-section">
            <div>
                <div>
                    <span class="overline">Ficha técnica</span>
                    <h2>Lo que necesita saber</h2>
                    <div class="spec-grid">
                        @foreach ($producto->fichaTecnica() as $dato => $valor)
                            <div>
                                <span>{{ $dato }}</span>
                                <strong>{{ $valor }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>
                @if ($producto->durabilidadTexto())
                    <div class="durability">
                        <x-icon name="shield" :size="34" />
                        <span>Durabilidad estimada</span>
                        <strong>{{ $producto->durabilidadTexto() }}</strong>
                        {{-- Ancho por clase (p-0 … p-100): la CSP no permite style="" --}}
                        <div class="duration-bar"><i class="p-{{ $producto->durabilidadPorcentaje() }}"></i></div>
                        <small>{{ $producto->esMetal() ? 'A la intemperie y con instalación adecuada.' : 'En contacto con suelo y con instalación adecuada.' }}</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</main>
@endsection
