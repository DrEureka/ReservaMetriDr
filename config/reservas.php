<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Horarios de atención por día
    |--------------------------------------------------------------------------
    | Clave: día de semana en formato Carbon (0=domingo .. 6=sábado).
    | Para sábado el rango cruza medianoche: 'cruza_medianoche' => true.
    */

    'horarios_por_dia' => [
        1 => ['inicio' => '10:00', 'fin' => '24:00', 'cruza_medianoche' => false],
        2 => ['inicio' => '10:00', 'fin' => '24:00', 'cruza_medianoche' => false],
        3 => ['inicio' => '10:00', 'fin' => '24:00', 'cruza_medianoche' => false],
        4 => ['inicio' => '10:00', 'fin' => '24:00', 'cruza_medianoche' => false],
        5 => ['inicio' => '10:00', 'fin' => '24:00', 'cruza_medianoche' => false],
        6 => ['inicio' => '22:00', 'fin' => '02:00', 'cruza_medianoche' => true],
        0 => ['inicio' => '12:00', 'fin' => '16:00', 'cruza_medianoche' => false],
    ],

    /*
    |--------------------------------------------------------------------------
    | Reglas de reservas
    |--------------------------------------------------------------------------
    */

    'duracion_minutos'             => 120,
    'anticipacion_minima_minutos'  => 15,
    'max_mesas_por_reserva'        => 3,
];
