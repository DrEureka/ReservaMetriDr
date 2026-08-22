<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'fecha'             => ['required', 'date', 'after_or_equal:today'],
            'hora_inicio'       => ['required', 'date_format:H:i'],
            'cantidad_personas' => ['required', 'integer', 'min:1', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha.required'             => 'Elegí una fecha.',
            'fecha.date'                 => 'La fecha no es válida.',
            'fecha.after_or_equal'       => 'La fecha tiene que ser hoy o posterior.',
            'hora_inicio.required'       => 'Elegí un horario.',
            'hora_inicio.date_format'    => 'El horario tiene que tener formato HH:MM.',
            'cantidad_personas.required' => 'Indicá la cantidad de personas.',
            'cantidad_personas.min'      => 'La cantidad de personas tiene que ser al menos 1.',
            'cantidad_personas.max'      => 'La cantidad máxima es 50 personas.',
        ];
    }
}
