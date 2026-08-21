<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMesaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $mesaId = $this->route('mesa');

        return [
            'ubicacion' => ['required', 'string', 'in:A,B,C,D'],
            'numero'    => [
                'required',
                'integer',
                'min:1',
                'max:999',
                Rule::unique('mesas', 'numero')
                    ->where('ubicacion', $this->input('ubicacion'))
                    ->ignore($mesaId),
            ],
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
            'numero.unique'      => 'Ya existe una mesa con ese número en la ubicación elegida.',
            'capacidad.required' => 'Indicá la capacidad.',
            'capacidad.min'      => 'La capacidad tiene que ser al menos 1 persona.',
        ];
    }

    public function attributes(): array
    {
        return [
            'numero' => 'número',
        ];
    }
}
