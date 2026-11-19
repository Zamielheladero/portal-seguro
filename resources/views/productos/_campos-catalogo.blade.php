{{-- Diseño AgroPoste: datos opcionales para el catálogo, los filtros y la ficha técnica. --}}
<fieldset class="campos-catalogo">
    <legend>Datos para el catálogo (opcionales)</legend>

    @foreach (['categoria', 'tratamiento', 'uso'] as $campo)
        <label for="{{ $campo }}">{{ App\Models\Producto::FILTROS[$campo][0] }}</label>
        <select id="{{ $campo }}" name="{{ $campo }}">
            <option value="">Sin definir</option>
            @foreach (App\Models\Producto::FILTROS[$campo][1] as $valor => $texto)
                <option value="{{ $valor }}" @selected(old($campo, $producto?->{$campo}) === $valor)>{{ $texto }}</option>
            @endforeach
        </select>
        @error($campo) <p>{{ $message }}</p> @enderror
    @endforeach

    <label for="material">Material</label>
    <input type="text" id="material" name="material" maxlength="100" placeholder="Ej. Eucalipto inmunizado" value="{{ old('material', $producto?->material) }}">
    @error('material') <p>{{ $message }}</p> @enderror

    <label for="medida">Medidas</label>
    <input type="text" id="medida" name="medida" maxlength="100" placeholder="Ej. 2,2 m × 10–12 cm" value="{{ old('medida', $producto?->medida) }}">
    @error('medida') <p>{{ $message }}</p> @enderror

    <label for="largo_m">Largo en metros (para el filtro de medidas)</label>
    <input type="number" id="largo_m" name="largo_m" step="0.01" min="0" max="99" value="{{ old('largo_m', $producto?->largo_m) }}">
    @error('largo_m') <p>{{ $message }}</p> @enderror

    <label for="etiqueta">Etiqueta de la tarjeta</label>
    <input type="text" id="etiqueta" name="etiqueta" maxlength="40" placeholder="Ej. Más vendido" value="{{ old('etiqueta', $producto?->etiqueta) }}">
    @error('etiqueta') <p>{{ $message }}</p> @enderror

    <label for="durabilidad_min">Durabilidad estimada (años, mínimo y máximo)</label>
    <div class="campos-dobles">
        <input type="number" id="durabilidad_min" name="durabilidad_min" min="0" max="99" aria-label="Durabilidad mínima" value="{{ old('durabilidad_min', $producto?->durabilidad_min) }}">
        <input type="number" id="durabilidad_max" name="durabilidad_max" min="0" max="99" aria-label="Durabilidad máxima" value="{{ old('durabilidad_max', $producto?->durabilidad_max) }}">
    </div>
    @error('durabilidad_min') <p>{{ $message }}</p> @enderror
    @error('durabilidad_max') <p>{{ $message }}</p> @enderror
</fieldset>
