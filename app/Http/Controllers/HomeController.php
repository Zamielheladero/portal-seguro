<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    /**
     * Respuestas de la asesoría => valor de la columna productos.uso.
     */
    private const USOS_ASESORIA = [
        'potrero' => 'cerca',
        'corral' => 'corral',
        'construccion' => 'construccion',
    ];

    public function index()
    {
        $destacados = Producto::disponibles()->latest()->latest('id')->take(3)->get();

        return view('home', compact('destacados'));
    }

    public function about()
    {
        return view('about');
    }

    public function contact()
    {
        return view('contact');
    }

    public function sendContact(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        // En esta semana no enviamos correo todavía.
        // Trabajamos únicamente con datos ya validados.
        return back()->with('status', 'Mensaje validado correctamente.');
    }

    /**
     * Diseño AgroPoste - Transporte: consulta de cobertura por municipio.
     * Se compara contra la lista de config/agroposte.php (no toca la base de datos).
     */
    public function transporte(Request $request)
    {
        $busqueda = Str::limit(trim((string) $request->query('municipio', '')), 60, '');
        $resultado = null;

        if ($busqueda !== '') {
            $resultado = ['busqueda' => $busqueda, 'departamento' => null, 'municipio' => null];
            $normalizado = Str::lower(Str::ascii($busqueda));

            foreach (config('agroposte.coverage') as $departamento => $municipios) {
                foreach ($municipios as $municipio) {
                    if (Str::lower(Str::ascii($municipio)) === $normalizado) {
                        $resultado['departamento'] = $departamento;
                        $resultado['municipio'] = $municipio;
                        break 2;
                    }
                }
            }
        }

        $totalMunicipios = collect(config('agroposte.coverage'))->flatten()->count();

        return view('paginas.info', ['tipo' => 'transporte', 'resultado' => $resultado, 'totalMunicipios' => $totalMunicipios]);
    }

    public function garantias()
    {
        return view('paginas.info', ['tipo' => 'garantias']);
    }

    /**
     * Diseño AgroPoste - Asesoría: recomienda un producto disponible según uso y suelo.
     * Solo se aceptan valores de la lista blanca; cualquier otro se ignora.
     */
    public function asesoria(Request $request)
    {
        $uso = $request->query('uso');
        $suelo = $request->query('suelo');
        $recomendado = null;
        $respondio = is_string($uso) && is_string($suelo)
            && isset(self::USOS_ASESORIA[$uso]) && in_array($suelo, ['humedo', 'seco', 'nose'], true);

        if ($respondio) {
            $consulta = Producto::disponibles()->where('uso', self::USOS_ASESORIA[$uso]);

            $recomendado = (match ($suelo) {
                // Suelo húmedo: la madera que más dura en contacto con agua.
                'humedo' => $consulta->whereIn('tratamiento', ['inmunizado', 'natural'])->orderByDesc('durabilidad_max'),
                // Suelo seco: la opción más económica sirve bien.
                'seco' => $consulta->orderBy('precio'),
                default => $consulta->orderByDesc('durabilidad_max'),
            })->first();
        }

        return view('paginas.info', [
            'tipo' => 'asesoria',
            'uso' => $uso,
            'suelo' => $suelo,
            'respondio' => $respondio,
            'recomendado' => $recomendado,
        ]);
    }
}
