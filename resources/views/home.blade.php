@extends('layouts.app')

@section('title', 'Inicio')
@section('pagina_completa', '1')

@section('content')
<main>
    <section class="hero">
        <div class="hero-image"></div>
        <div class="container hero-content">
            <div class="hero-copy">
                <div class="trust-pill">
                    <x-icon name="shield" :size="18" /> Madera seleccionada y garantizada
                </div>
                <h1>Estacones para su finca, <em>sin vueltas.</em></h1>
                <p>Cotice postes, madera y cercas con precios claros. Se los llevamos hasta su finca en Ubaté.</p>
                <div class="hero-buttons">
                    <a href="{{ route('cotizacion.show') }}" class="btn btn-amber">
                        <x-icon name="calculator" :size="20" /><span>Cotizar ahora</span>
                    </a>
                    <a href="{{ route('catalogo.index') }}" class="btn btn-secondary"><span>Ver catálogo</span></a>
                </div>
                <div class="hero-proof">
                    <span><x-icon name="check" :size="18" /> Precios claros</span>
                    <span><x-icon name="check" :size="18" /> Asesoría sin costo</span>
                </div>
            </div>
        </div>
    </section>

    <section class="how section">
        <div class="container">
            <div class="section-heading">
                <span>Fácil y rápido</span>
                <h2>De la pantalla a su finca en 3 pasos</h2>
                <p>Sin formularios largos ni llamadas innecesarias.</p>
            </div>
            <div class="steps">
                @foreach ([
                    ['01', 'Elija lo que necesita', 'Compare medidas, maderas y precios con información fácil de entender.', 'fence'],
                    ['02', 'Calcule su cotización', 'Indique cantidades y su vereda. Le mostramos todo antes de solicitar.', 'calculator'],
                    ['03', 'Reciba en su finca', 'Coordinamos la entrega y le avisamos cuándo llega su pedido.', 'truck'],
                ] as [$numero, $titulo, $descripcion, $icono])
                    <div class="step">
                        <span class="step-number">{{ $numero }}</span>
                        <div class="step-icon"><x-icon :name="$icono" /></div>
                        <h3>{{ $titulo }}</h3>
                        <p>{{ $descripcion }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="featured section">
        <div class="container">
            <div class="section-row">
                <div>
                    <span class="overline">Lo más pedido</span>
                    <h2>Productos para cada trabajo</h2>
                </div>
                <a href="{{ route('catalogo.index') }}" class="text-link">
                    Ver todo el catálogo <x-icon name="arrow" :size="18" />
                </a>
            </div>
            <div class="product-grid home-products">
                @forelse ($destacados as $producto)
                    <x-producto-card :producto="$producto" />
                @empty
                    <p class="muted">Pronto tendremos productos disponibles.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="benefits section">
        <div class="container benefits-grid">
            <div class="benefit-story">
                <span class="overline">Compra con confianza</span>
                <h2>Material que aguanta el trabajo del campo</h2>
                <p>Seleccionamos cada pieza y le explicamos en palabras sencillas cuánto dura, para qué sirve y cómo cuidarla.</p>
                <a href="{{ route('garantias') }}" class="btn btn-secondary"><span>Conocer nuestras garantías</span></a>
            </div>
            <div class="benefit-list">
                @foreach ([
                    ['shield', 'Garantía que sí se entiende', 'Le decimos qué cubre y por cuánto tiempo.'],
                    ['truck', 'Transporte calculado', 'Conozca el costo aproximado antes de solicitar.'],
                    ['phone', 'Una persona le responde', 'Asesores que conocen de cercas, corrales y madera.'],
                    ['leaf', 'Madera de origen responsable', 'Proveedores seleccionados y tratamientos certificados.'],
                ] as [$icono, $titulo, $descripcion])
                    <div class="benefit">
                        <span><x-icon :name="$icono" /></span>
                        <div>
                            <h3>{{ $titulo }}</h3>
                            <p>{{ $descripcion }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="testimonial section">
        <div class="container">
            <div class="quote-mark">“</div>
            <blockquote>Pedí 120 estacones para una cerca en Ubaté. Me explicaron cuál me servía, vi el precio completo y el camión llegó el día acordado.</blockquote>
            <div class="farmer">
                <span>JM</span>
                <div>
                    <strong>José Manuel Rodríguez</strong>
                    <small>Ganadero · Ubaté, Cundinamarca</small>
                </div>
            </div>
        </div>
    </section>

    <section class="cta">
        <div class="container">
            <div>
                <span>¿Listo para empezar?</span>
                <h2>Calcule hoy cuánto cuesta su cerca</h2>
                <p>Solo necesita las medidas del terreno. Nosotros le ayudamos con lo demás.</p>
            </div>
            <a href="{{ route('cotizacion.show') }}" class="btn btn-amber">
                <x-icon name="calculator" :size="20" /><span>Hacer mi cotización</span>
            </a>
        </div>
    </section>
</main>
@endsection
