@extends('layouts.app')

@section('title', 'Catálogo')
@section('pagina_completa', '1')

@section('content')
<main class="page-bg">
    {{-- Los campos usan form="filtros-catalogo" para no anidar este formulario con los de "Agregar". --}}
    <form id="filtros-catalogo" method="GET" action="{{ route('catalogo.index') }}" data-autosubmit></form>

    <div class="container catalog-head">
        <div>
            <span class="breadcrumb"><a href="{{ route('home') }}">Inicio</a> / Catálogo</span>
            <h1>Productos para su finca</h1>
            <p>Compare materiales, medidas y precios. Todos disponibles para cotizar.</p>
        </div>
        <label class="search-box">
            <x-icon name="search" />
            <input type="search" form="filtros-catalogo" name="q" value="{{ $busqueda }}" maxlength="60" aria-label="Buscar productos" placeholder="Busque estacones, tablas, alambre...">
        </label>
    </div>

    <div class="container catalog-layout">
        <aside class="filters" data-filters>
            <div class="filter-title">
                <h3>Filtrar productos</h3>
                <button type="button" data-filter-close aria-label="Cerrar filtros"><x-icon name="x" /></button>
            </div>

            @foreach (App\Models\Producto::FILTROS as $campo => [$titulo, $opciones])
                <fieldset class="filter-group">
                    <h4>{{ $titulo }}</h4>
                    @foreach ($opciones as $valor => $texto)
                        <label>
                            <input type="checkbox" form="filtros-catalogo" name="{{ $campo }}[]" value="{{ $valor }}" @checked(in_array($valor, $seleccion[$campo], true))>
                            <span>{{ $texto }}</span>
                            <small>{{ $conteos[$campo][$valor] ?? 0 }}</small>
                        </label>
                    @endforeach
                </fieldset>
            @endforeach

            <button type="submit" form="filtros-catalogo" class="btn btn-primary mobile-only"><span>Ver {{ $productos->total() }} productos</span></button>
            <noscript><button type="submit" form="filtros-catalogo" class="btn btn-primary"><span>Aplicar filtros</span></button></noscript>
        </aside>

        <section class="catalog-results">
            <div class="catalog-tools">
                <button type="button" class="filter-trigger" data-filter-open>
                    <x-icon name="sliders" /> Filtros
                    @if ($filtrosActivos)
                        <b>{{ $filtrosActivos }}</b>
                    @endif
                </button>
                <span>{{ $productos->total() }} {{ $productos->total() === 1 ? 'producto' : 'productos' }}</span>
                <label>
                    Ordenar:
                    <select form="filtros-catalogo" name="orden">
                        @foreach (App\Models\Producto::ORDENES as $valor => $texto)
                            <option value="{{ $valor }}" @selected($orden === $valor)>{{ $texto }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            @if ($filtrosActivos || $busqueda !== '')
                <div class="applied">
                    @if ($busqueda !== '')
                        <span>
                            “{{ $busqueda }}”
                            <a href="{{ request()->fullUrlWithoutQuery(['q', 'page']) }}" aria-label="Quitar búsqueda">×</a>
                        </span>
                    @endif
                    @foreach ($seleccion as $campo => $valores)
                        @foreach ($valores as $valor)
                            <span>
                                {{ App\Models\Producto::FILTROS[$campo][1][$valor] }}
                                <a href="{{ route('catalogo.index', array_merge(request()->except('page'), [$campo => array_values(array_diff($valores, [$valor]))])) }}" aria-label="Quitar filtro">×</a>
                            </span>
                        @endforeach
                    @endforeach
                    <a href="{{ route('catalogo.index') }}">Limpiar filtros</a>
                </div>
            @endif

            @if ($productos->isEmpty())
                <div class="panel empty-state">
                    <h2>No encontramos productos</h2>
                    <p>{{ $filtrosActivos || $busqueda !== '' ? 'Pruebe con otra búsqueda o pregúntele a un asesor.' : 'No hay productos disponibles por el momento.' }}</p>
                    <a href="{{ route('asesoria') }}" class="btn btn-secondary"><span>Hablar con un asesor</span></a>
                </div>
            @else
                <div class="product-grid">
                    @foreach ($productos as $producto)
                        <x-producto-card :producto="$producto" />
                    @endforeach
                </div>
                {{ $productos->links('partials.paginacion') }}
            @endif
        </section>
    </div>
</main>
@endsection
