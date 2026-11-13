<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // H-SAST: antes estaba vacío, así que validated() devolvía [] y la
        // edición no se validaba ni se guardaba. Mismas reglas que al crear.
        return [
            'title' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string', 'max:10000'],
        ];
    }
}
