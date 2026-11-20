{{-- Paginación simple con el estilo AgroPoste (sin clases de Tailwind). --}}
@if ($paginator->hasPages())
    <nav class="paginacion" aria-label="Páginas del catálogo">
        @if ($paginator->onFirstPage())
            <span class="deshabilitado">Anterior</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev">Anterior</a>
        @endif

        <span>Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}</span>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next">Siguiente</a>
        @else
            <span class="deshabilitado">Siguiente</span>
        @endif
    </nav>
@endif
