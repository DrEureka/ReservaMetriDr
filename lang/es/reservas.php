<?php

return [

    /*
    | Dominio: Reservas
    */

    'ubicaciones' => [
        'A' => 'Sección A',
        'B' => 'Sección B',
        'C' => 'Sección C',
        'D' => 'Sección D',
    ],

    'horarios' => [
        'lun-vie' => 'Lunes a viernes: 10:00 a 24:00 hs',
        'sab'     => 'Sábado: 22:00 a 02:00 hs',
        'dom'     => 'Domingo: 12:00 a 16:00 hs',
    ],

    'errors' => [
        'sin_mesas'        => 'No hay mesas suficientes para :personas personas en la fecha y hora elegidas.',
        'fuera_de_horario' => 'La hora seleccionada está fuera del horario de atención.',
        'muy_tarde'        => 'Las reservas tienen que hacerse con al menos :minutos minutos de anticipación.',
        'ya_reservada'     => 'La mesa o sector solicitado ya no está disponible.',
    ],
];
