<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Paso 2 de la cotización: datos de contacto y lugar de entrega.
 * Solo entregamos en Ubaté: el municipio lo pone el servidor y el cliente indica su vereda.
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
            'vereda' => ['required', 'string', 'min:3', 'max:80'],
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
            'vereda.required' => 'Escriba la vereda o el sector de Ubaté donde queda su finca.',
            'vereda.min' => 'Escriba el nombre completo de la vereda o el sector.',
            'vereda.max' => 'La vereda puede tener máximo 80 caracteres.',
            'indicaciones.max' => 'Las indicaciones pueden tener máximo 500 caracteres.',
        ];
    }
}
