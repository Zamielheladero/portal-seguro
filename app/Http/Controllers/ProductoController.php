<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    /**
     * Listado de productos para el panel de administración
     * (a diferencia del catálogo público, aquí se muestran todos,
     * incluso inactivos o sin stock).
     */
    public function index()
    {
        $this->authorize('viewAny', Producto::class);

        $productos = Producto::latest()->paginate(15);

        return view('productos.index', compact('productos'));
    }

    /**
     * Formulario para crear un producto nuevo.
     */
    public function create()
    {
        $this->authorize('create', Producto::class);

        return view('productos.create');
    }

    /**
     * Guarda el producto nuevo, incluyendo la imagen si se subió una.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Producto::class);

        $data = $request->validate($this->reglas());

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $data['activo'] = $request->boolean('activo', true);

        Producto::create($data);

        return redirect()
            ->route('productos.index')
            ->with('status', 'Producto creado correctamente.');
    }

    /**
     * Formulario para editar un producto existente.
     * Cualquier usuario autenticado puede entrar aquí (ver ProductoPolicy::update).
     */
    public function edit(Producto $producto)
    {
        $this->authorize('update', $producto);

        return view('productos.edit', compact('producto'));
    }

    /**
     * Actualiza el producto. Si se sube una imagen nueva, borra la anterior
     * del disco para no dejar archivos huérfanos.
     */
    public function update(Request $request, Producto $producto)
    {
        $this->authorize('update', $producto);

        $data = $request->validate($this->reglas());

        if ($request->hasFile('imagen')) {
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $data['activo'] = $request->boolean('activo', true);

        $producto->update($data);

        return redirect()
            ->route('productos.index')
            ->with('status', 'Producto actualizado correctamente.');
    }

    /**
     * Elimina el producto (solo admin, según ProductoPolicy::delete)
     * y borra también su imagen del disco.
     */
    public function destroy(Producto $producto)
    {
        $this->authorize('delete', $producto);

        if ($producto->imagen) {
            Storage::disk('public')->delete($producto->imagen);
        }

        $producto->delete();

        return redirect()
            ->route('productos.index')
            ->with('status', 'Producto eliminado correctamente.');
    }

    /**
     * CMS-11 - Catálogo público: cualquier visitante puede verlo, sin login.
     * Solo muestra productos activos y con stock disponible.
     * Diseño AgroPoste: búsqueda, filtros y orden. Solo se aceptan valores
     * de las listas blancas Producto::FILTROS y Producto::ORDENES.
     */
    public function catalogo(Request $request)
    {
        $seleccion = $this->filtrosSeleccionados($request);
        $busqueda = mb_substr(trim((string) $request->query('q', '')), 0, 60);
        $orden = array_key_exists((string) $request->query('orden'), Producto::ORDENES)
            ? (string) $request->query('orden')
            : 'recientes';

        $productos = Producto::disponibles()
            ->when($busqueda !== '', function (Builder $consulta) use ($busqueda) {
                $termino = '%'.$busqueda.'%';
                $consulta->where(fn (Builder $q) => $q
                    ->where('nombre', 'like', $termino)
                    ->orWhere('material', 'like', $termino)
                    ->orWhere('descripcion', 'like', $termino));
            })
            ->when($seleccion['categoria'], fn (Builder $q, array $v) => $q->whereIn('categoria', $v))
            ->when($seleccion['tratamiento'], fn (Builder $q, array $v) => $q->whereIn('tratamiento', $v))
            ->when($seleccion['uso'], fn (Builder $q, array $v) => $q->whereIn('uso', $v))
            ->when($seleccion['largo'], function (Builder $q, array $v) {
                $q->where(function (Builder $dentro) use ($v) {
                    if (in_array('corto', $v, true)) {
                        $dentro->orWhereBetween('largo_m', [2, 2.5]);
                    }
                    if (in_array('largo', $v, true)) {
                        $dentro->orWhere('largo_m', '>=', 3);
                    }
                });
            })
            ->when($orden === 'menor-precio', fn (Builder $q) => $q->orderBy('precio'))
            ->when($orden === 'mayor-duracion', fn (Builder $q) => $q->orderByDesc('durabilidad_max'))
            ->latest()
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('catalogo.index', [
            'productos' => $productos,
            'conteos' => $this->conteosFiltros(),
            'seleccion' => $seleccion,
            'filtrosActivos' => collect($seleccion)->flatten()->count(),
            'busqueda' => $busqueda,
            'orden' => $orden,
        ]);
    }

    /**
     * CMS-11 - Detalle público de un producto.
     * Si el producto no está disponible (inactivo o sin stock), da 404
     * en vez de mostrarlo, aunque alguien adivine la URL.
     */
    public function show(Producto $producto)
    {
        abort_unless(
            $producto->activo && $producto->stock > 0,
            404
        );

        $relacionados = Producto::disponibles()
            ->whereKeyNot($producto->id)
            ->orderByRaw('categoria = ? desc', [$producto->categoria])
            ->latest()
            ->take(2)
            ->get();

        return view('catalogo.show', compact('producto', 'relacionados'));
    }

    /**
     * Reglas compartidas por crear y editar (panel).
     *
     * @return array<string, mixed>
     */
    private function reglas(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:2000'],
            'precio' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'imagen' => ['nullable', 'image', 'max:2048'], // máx. 2MB
            'activo' => ['sometimes', 'boolean'],
            // Diseño AgroPoste: datos del catálogo (todos opcionales)
            'categoria' => ['nullable', Rule::in(array_keys(Producto::FILTROS['categoria'][1]))],
            'tratamiento' => ['nullable', Rule::in(array_keys(Producto::FILTROS['tratamiento'][1]))],
            'uso' => ['nullable', Rule::in(array_keys(Producto::FILTROS['uso'][1]))],
            'material' => ['nullable', 'string', 'max:100'],
            'medida' => ['nullable', 'string', 'max:100'],
            'largo_m' => ['nullable', 'numeric', 'min:0', 'max:99'],
            'etiqueta' => ['nullable', 'string', 'max:40'],
            'durabilidad_min' => ['nullable', 'integer', 'min:0', 'max:99'],
            'durabilidad_max' => ['nullable', 'integer', 'min:0', 'max:99', 'gte:durabilidad_min'],
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    private function filtrosSeleccionados(Request $request): array
    {
        $seleccion = [];

        foreach (Producto::FILTROS as $campo => [, $opciones]) {
            $valores = $request->query($campo, []);
            $seleccion[$campo] = array_values(array_intersect(
                is_array($valores) ? $valores : [$valores],
                array_keys($opciones),
            ));
        }

        return $seleccion;
    }

    /**
     * Cuántos productos disponibles hay por cada opción de filtro.
     *
     * @return array<string, array<string, int>>
     */
    private function conteosFiltros(): array
    {
        $productos = Producto::disponibles()->get(['categoria', 'tratamiento', 'uso', 'largo_m']);

        return [
            'categoria' => $productos->countBy('categoria')->all(),
            'tratamiento' => $productos->countBy('tratamiento')->all(),
            'uso' => $productos->countBy('uso')->all(),
            'largo' => [
                'corto' => $productos->filter(fn (Producto $p) => $p->largo_m !== null && $p->largo_m >= 2 && $p->largo_m <= 2.5)->count(),
                'largo' => $productos->filter(fn (Producto $p) => $p->largo_m !== null && $p->largo_m >= 3)->count(),
            ],
        ];
    }
}
