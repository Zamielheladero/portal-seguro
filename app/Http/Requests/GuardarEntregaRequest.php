<?php

namespace App\Http\Requests;

use App\Models\Cotizacion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Paso 2 de la cotización: datos de contacto y lugar de entrega.
 * Solo entregamos en Ubaté: el municipio lo pone el servidor y el cliente indica su vereda.
 * Si elige recoger en el punto de venta, la vereda no se pide y no se cobra transporte.
 */
class GuardarEntregaRequest extends FormRequest
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
            'nombre_cliente' => ['required', 'string', 'min:3', 'max:80'],
            'telefono' => ['required', 'string', 'regex:/^[0-9 +()-]{7,20}$/'],
            'tipo_entrega' => ['required', Rule::in([Cotizacion::DOMICILIO, Cotizacion::RECOGER])],
            'vereda' => ['nullable', 'required_if:tipo_entrega,'.Cotizacion::DOMICILIO, 'string', 'min:3', 'max:80'],
            'indicaciones' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre_cliente.required' => 'Escriba su nombre.',
            'nombre_cliente.min' => 'Escriba su nombre completo.',
            'telefono.required' => 'Escriba un celular para contactarle.',
            'telefono.regex' => 'Escriba un número de celular válido.',
            'tipo_entrega.required' => 'Elija si quiere la entrega en su finca o recogerlo.',
            'tipo_entrega.in' => 'Elija una opción de entrega de la lista.',
            'vereda.required_if' => 'Escriba la vereda o el sector de Ubaté donde queda su finca.',
            'vereda.min' => 'Escriba el nombre completo de la vereda o el sector.',
            'vereda.max' => 'La vereda puede tener máximo 80 caracteres.',
            'indicaciones.max' => 'Las indicaciones pueden tener máximo 500 caracteres.',
        ];
    }
}
