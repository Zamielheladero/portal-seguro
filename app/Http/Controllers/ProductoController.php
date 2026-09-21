<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    /**
     * Listado de productos para el panel de administración
     * (a diferencia del catálogo público, aquí se muestran todos,
     * incluso inactivos o sin stock).
     */
    public function index()
    {
        $productos = Producto::latest()->paginate(15);

        return view('productos.index', compact('productos'));
    }

    /**
     * Formulario para crear un producto nuevo.
     */
    public function create()
    {
        return view('productos.create');
    }

    /**
     * Guarda el producto nuevo, incluyendo la imagen si se subió una.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:2000'],
            'precio' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'imagen' => ['nullable', 'image', 'max:2048'], // máx. 2MB
            'activo' => ['sometimes', 'boolean'],
        ]);

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

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:2000'],
            'precio' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'imagen' => ['nullable', 'image', 'max:2048'],
            'activo' => ['sometimes', 'boolean'],
        ]);

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
     */
    public function catalogo()
    {
        $productos = Producto::disponibles()->latest()->paginate(12);

        return view('catalogo.index', compact('productos'));
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

        return view('catalogo.show', compact('producto'));
    }
}