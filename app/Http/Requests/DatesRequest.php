<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DatesRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ];
    }

    public function messages(): array
    {
        return [
            'startDate.required' => 'El campo fecha de inicio es obligatorio.',
            'startDate.date' => 'El campo fecha de inicio debe ser una fecha válida.',
            'endDate.required' => 'El campo fecha de finalización es obligatorio.',
            'endDate.date' => 'El campo fecha de finalización debe ser una fecha válida.',
            'endDate.after_or_equal' => 'La fecha de finalización no puede ser anterior a la fecha de inicio.',
        ];
    }
}
