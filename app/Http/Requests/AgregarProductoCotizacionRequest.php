<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Agregar un producto a la cotización (público, sin login).
 */
class AgregarProductoCotizacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Solo productos que se ven en el catálogo público.
            'producto_id' => ['required', 'integer', Rule::exists('productos', 'id')->where(fn ($q) => $q->where('activo', true)->where('stock', '>', 0))],
            'cantidad' => ['sometimes', 'integer', 'min:1', 'max:'.config('agroposte.max_quantity')],
            'ir_a_cotizacion' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'producto_id.exists' => 'Ese producto ya no está disponible.',
            'cantidad.min' => 'La cantidad mínima es 1.',
            'cantidad.max' => 'Para más de :max unidades hable con un asesor.',
        ];
    }
}
