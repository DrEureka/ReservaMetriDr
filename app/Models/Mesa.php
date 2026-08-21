<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Mesa extends Model
{
    use HasFactory;

    protected $table = 'mesas';

    protected $fillable = [
        'ubicacion',
        'numero',
        'capacidad',
    ];

    protected function casts(): array
    {
        return [
            'numero'    => 'integer',
            'capacidad' => 'integer',
        ];
    }

    public const UBICACIONES = ['A', 'B', 'C', 'D'];

    public function reservas(): BelongsToMany
    {
        return $this->belongsToMany(Reserva::class, 'reserva_mesa')
            ->withTimestamps();
    }

    public function nombreCompleto(): string
    {
        return "{$this->ubicacion}-{$this->numero}";
    }

    public function scopeUbicacion($consulta, string $ubicacion)
    {
        return $consulta->where('ubicacion', $ubicacion);
    }
}
