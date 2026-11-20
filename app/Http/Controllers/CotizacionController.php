<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActualizarCantidadCotizacionRequest;
use App\Http\Requests\AgregarProductoCotizacionRequest;
use App\Http\Requests\CalcularPostesRequest;
use App\Http\Requests\GuardarEntregaRequest;
use App\Models\Cotizacion;
use App\Models\Producto;
use App\Services\Cotizador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Diseño AgroPoste - Cotización pública en 3 pasos (productos, entrega, resumen).
 * La cotización en curso vive en la sesión; al enviarla se guarda en la base de datos.
 */
class CotizacionController extends Controller
{
    public function show(Request $request, Cotizador $cotizador)
    {
        $paso = (int) $request->query('paso', 1);
        $paso = in_array($paso, [1, 2, 3], true) ? $paso : 1;

        if ($paso > 1 && $cotizador->vacia()) {
            return redirect()->route('cotizacion.show');
        }

        if ($paso === 3 && $cotizador->entrega() === null) {
            return redirect()->route('cotizacion.show', ['paso' => 2]);
        }

        return view('cotizacion.show', [
            'paso' => $paso,
            'items' => $cotizador->items(),
            'totales' => $cotizador->totales(),
            'entrega' => $cotizador->entrega(),
            'zona' => config('agroposte.zona'),
        ]);
    }

    public function agregar(AgregarProductoCotizacionRequest $request, Cotizador $cotizador)
    {
        $producto = Producto::findOrFail($request->integer('producto_id'));
        $cotizador->agregar($producto, $request->integer('cantidad', 1));

        if ($request->boolean('ir_a_cotizacion')) {
            return redirect()->route('cotizacion.show');
        }

        return back()->with('aviso', "{$producto->nombre} se agregó a su cotización.");
    }

    public function actualizar(ActualizarCantidadCotizacionRequest $request, Producto $producto, Cotizador $cotizador)
    {
        $cotizador->fijar($producto, $request->integer('cantidad'));

        return redirect()->route('cotizacion.show');
    }

    /**
     * Calculadora: un estacón por tramo de cerca, más dos de esquina.
     */
    public function calcular(CalcularPostesRequest $request, Cotizador $cotizador)
    {
        $postes = (int) ceil($request->float('metros') / $request->float('distancia')) + 2;

        $estacon = $cotizador->items()
            ->map(fn (array $item) => $item['producto'])
            ->first(fn (Producto $producto) => $producto->categoria === 'estacones')
            ?? Producto::disponibles()->where('categoria', 'estacones')->latest()->first();

        if (! $estacon) {
            return redirect()->route('cotizacion.show')
                ->withErrors(['metros' => 'Ahora no hay estacones disponibles. Escríbale a un asesor.'])
                ->withInput();
        }

        $cotizador->fijar($estacon, $postes);

        return redirect()->route('cotizacion.show')
            ->with('aviso', "Para {$request->input('metros')} m de cerca necesita unas {$postes} unidades de {$estacon->nombre}. Ya las dejamos en su cotización.")
            ->withInput();
    }

    public function guardarEntrega(GuardarEntregaRequest $request, Cotizador $cotizador)
    {
        // Departamento y municipio los fija el servidor: solo se entrega en Ubaté.
        $cotizador->guardarEntrega([
            ...$request->safe()->only(['nombre_cliente', 'telefono', 'vereda', 'indicaciones']),
            'departamento' => config('agroposte.zona.departamento'),
            'municipio' => config('agroposte.zona.municipio'),
        ]);

        return redirect()->route('cotizacion.show', ['paso' => 3]);
    }

    public function store(Request $request, Cotizador $cotizador)
    {
        if ($cotizador->vacia()) {
            return redirect()->route('cotizacion.show');
        }

        $entrega = $cotizador->entrega();

        if ($entrega === null) {
            return redirect()->route('cotizacion.show', ['paso' => 2])
                ->with('aviso', 'Antes de enviar, cuéntenos dónde queda su finca.');
        }

        $totales = $cotizador->totales();

        $cotizacion = DB::transaction(function () use ($cotizador, $entrega, $totales, $request) {
            $cotizacion = new Cotizacion([
                ...$entrega,
                ...$totales,
                'valida_hasta' => now()->addDays((int) config('agroposte.quote_valid_days')),
            ]);
            $cotizacion->user_id = $request->user()?->id;
            $cotizacion->save();

            $cotizacion->items()->createMany($cotizador->items()->map(fn (array $item) => [
                'producto_id' => $item['producto']->id,
                'nombre_producto' => $item['producto']->nombre,
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $item['producto']->precioEntero(),
                'total_linea' => $item['total'],
            ])->all());

            return $cotizacion;
        });

        $cotizador->vaciar();

        // Solo quien envió la cotización puede ver su confirmación (evita IDOR por código).
        $request->session()->put('cotizacion.ultima', $cotizacion->codigo);

        return redirect()->route('cotizacion.confirmacion', $cotizacion->codigo);
    }

    public function confirmacion(Request $request, string $codigo)
    {
        abort_unless($request->session()->get('cotizacion.ultima') === $codigo, 404);

        $cotizacion = Cotizacion::with('items')->where('codigo', $codigo)->firstOrFail();

        return view('cotizacion.confirmacion', compact('cotizacion'));
    }
}
