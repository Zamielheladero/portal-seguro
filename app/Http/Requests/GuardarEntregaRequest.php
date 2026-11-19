<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Paso 2 de la cotización: datos de contacto y lugar de entrega.
 * Departamento y municipio solo pueden ser los de la lista de cobertura.
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
        $cobertura = config('agroposte.coverage');

        return [
            'nombre_cliente' => ['required', 'string', 'min:3', 'max:80'],
            'telefono' => ['required', 'string', 'regex:/^[0-9 +()-]{7,20}$/'],
            'departamento' => ['required', 'string', Rule::in(array_keys($cobertura))],
            'municipio' => ['required', 'string', Rule::in($cobertura[$this->input('departamento')] ?? [])],
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
            'departamento.in' => 'Por ahora solo entregamos en Cundinamarca y Boyacá.',
            'municipio.required' => 'Elija su municipio.',
            'municipio.in' => 'Elija un municipio de la lista.',
            'indicaciones.max' => 'Las indicaciones pueden tener máximo 500 caracteres.',
        ];
    }
}
