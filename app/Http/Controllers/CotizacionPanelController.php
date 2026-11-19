<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Diseño AgroPoste - Cotizaciones recibidas, solo para personal interno
 * (la ruta exige 'auth' y 'role:admin,vendedor').
 */
class CotizacionPanelController extends Controller
{
    public const ESTADOS = ['pendiente', 'enviada', 'aprobada', 'rechazada'];

    public function index()
    {
        $cotizaciones = Cotizacion::withCount('items')->latest()->paginate(20);

        return view('cotizaciones.index', compact('cotizaciones'));
    }

    public function show(Cotizacion $cotizacion)
    {
        $cotizacion->load('items', 'user');

        return view('cotizaciones.show', compact('cotizacion'));
    }

    public function update(Request $request, Cotizacion $cotizacion)
    {
        $data = $request->validate([
            'estado' => ['required', Rule::in(self::ESTADOS)],
        ]);

        $cotizacion->estado = $data['estado'];
        $cotizacion->save();

        return redirect()
            ->route('cotizaciones.show', $cotizacion)
            ->with('status', 'Estado actualizado.');
    }
}
