<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Template Labels
    |--------------------------------------------------------------------------
    |
    | Estas etiquetas permiten adaptar la terminología del sistema a cualquier
    | giro de negocio sin modificar la lógica. Para conectarlas a las vistas,
    | se puede usar config('template.labels.service') en Blade.
    |
    | Ejemplo para viajes:   service => Tour,    participant => Pasajero
    | Ejemplo para cursos:   service => Curso,   participant => Alumno
    | Ejemplo para eventos:  service => Evento,  participant => Asistente
    |
    */

    'labels' => [
        'service'              => 'Tour',
        'services'             => 'Tours',
        'participant'          => 'Pasajero',
        'participants'         => 'Pasajeros',
        'resource'             => 'Asiento',
        'resources'            => 'Asientos',
        'reservation'          => 'Reservación',
        'reservations'         => 'Reservaciones',
        'payment_receipt'      => 'Voucher',
        'reservation_document' => 'Ticket',
        'meeting_point'        => 'Punto de Abordaje',
        'meeting_points'       => 'Puntos de Abordaje',
        'fleet'                => 'Flota',
        'business_tagline'     => 'Agencia de Viajes y Excursiones Premium',
    ],

    /*
    |--------------------------------------------------------------------------
    | Participant Types
    |--------------------------------------------------------------------------
    |
    | Tipos de participante disponibles en el sistema. Cada tipo puede tener
    | una regla de descuento asociada que se aplica en ReservationService.
    |
    | Para agregar o quitar tipos, editar este arreglo y ajustar la lógica
    | en ReservationService@calculatePassengerPricing().
    |
    */

    'participant_types' => [
        'Adulto',
        'Niño',
        'Adulto Mayor',
    ],

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    */

    'currency' => [
        'code'   => 'MXN',
        'symbol' => '$',
        'name'   => 'Pesos Mexicanos',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cleanup Command
    |--------------------------------------------------------------------------
    |
    | Nombre del comando Artisan para limpiar datos de prueba.
    | Usar con precaución y siempre con backup previo.
    |
    */

    'cleanup_command' => 'vbymex:clean-test-data',

];
