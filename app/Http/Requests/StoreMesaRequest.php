<?php

namespace App\Http\Requests;

use App\Models\Mesa;
use Illuminate\Foundation\Http\FormRequest;

class StoreMesaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'ubicacion' => ['required', 'string', 'in:A,B,C,D'],
            'numero'    => ['required', 'integer', 'min:1', 'max:999'],
            'capacidad' => ['required', 'integer', 'min:1', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'ubicacion.required' => 'Elegí una ubicación.',
            'ubicacion.in'       => 'La ubicación tiene que ser A, B, C o D.',
            'numero.required'    => 'Indicá el número de mesa.',
            'numero.integer'     => 'El número tiene que ser un entero.',
            'numero.min'         => 'El número tiene que ser mayor o igual a 1.',
            'numero.unique'      => 'Ya existe una mesa :attribute en la ubicación :ubicacion.',
            'capacidad.required' => 'Indicá la capacidad.',
            'capacidad.min'      => 'La capacidad tiene que ser al menos 1 persona.',
            'capacidad.max'      => 'La capacidad no puede superar las 50 personas.',
        ];
    }

    public function attributes(): array
    {
        return [
            'numero' => 'número',
        ];
    }
}
