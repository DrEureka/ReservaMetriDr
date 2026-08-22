<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Reserva extends Model
{
    use HasFactory;

    public const ESTADO_CONFIRMADA = 'confirmada';
    public const ESTADO_CANCELADA  = 'cancelada';

    public const UBICACIONES = ['A', 'B', 'C', 'D'];

    protected $fillable = [
        'user_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'ubicacion',
        'cantidad_personas',
        'estado',
        'cancelada_at',
    ];

    protected function casts(): array
    {
        return [
            'fecha'             => 'date',
            'hora_inicio'       => 'datetime:H:i',
            'hora_fin'          => 'datetime:H:i',
            'cantidad_personas' => 'integer',
            'cancelada_at'      => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mesas(): BelongsToMany
    {
        return $this->belongsToMany(Mesa::class, 'reserva_mesa')
            ->withTimestamps();
    }

    public function estaConfirmada(): bool
    {
        return $this->estado === self::ESTADO_CONFIRMADA;
    }

    public function estaCancelada(): bool
    {
        return $this->estado === self::ESTADO_CANCELADA;
    }

    public function nombresMesas(): string
    {
        return $this->mesas
            ->sortBy('numero')
            ->map(fn ($mesa) => $mesa->nombreCompleto())
            ->implode(', ');
    }
}
