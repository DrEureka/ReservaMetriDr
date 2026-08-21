<?php

return [

    /*
    | Dominio: Reservations (inglés)
    */

    'ubicaciones' => [
        'A' => 'Section A',
        'B' => 'Section B',
        'C' => 'Section C',
        'D' => 'Section D',
    ],

    'horarios' => [
        'lun-vie' => 'Monday to Friday: 10:00 AM to 12:00 AM',
        'sab'     => 'Saturday: 10:00 PM to 02:00 AM',
        'dom'     => 'Sunday: 12:00 PM to 04:00 PM',
    ],

    'errors' => [
        'sin_mesas'        => 'Not enough tables for :people people at the requested date and time.',
        'fuera_de_horario' => 'The selected time is outside business hours.',
        'muy_tarde'        => 'Reservations must be made at least :minutes minutes in advance.',
        'ya_reservada'     => 'The requested table or section is no longer available.',
    ],
];
