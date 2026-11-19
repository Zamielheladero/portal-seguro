<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalcularPostesRequest extends FormRequest
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
            'metros' => ['required', 'numeric', 'min:1', 'max:10000'],
            'distancia' => ['required', 'in:2,2.5,3'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'metros.required' => 'Escriba los metros de cerca.',
            'metros.numeric' => 'Escriba solo números.',
            'metros.min' => 'Escriba al menos 1 metro.',
            'metros.max' => 'Para más de 10.000 metros hable con un asesor.',
            'distancia.in' => 'Elija una distancia de la lista.',
        ];
    }
}
