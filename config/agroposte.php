<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Contacto
    |--------------------------------------------------------------------------
    */

    'phone' => env('AGROPOSTE_PHONE', '(601) 742 0931'),

    // Número en formato internacional sin "+" ni espacios, para enlaces wa.me.
    'whatsapp' => env('AGROPOSTE_WHATSAPP', '573000000000'),

    'advisor' => [
        'name' => 'Laura Martínez',
        'initials' => 'LM',
        'role' => 'Especialista en cercas ganaderas',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cotizaciones
    |--------------------------------------------------------------------------
    */

    // Transporte estimado en pesos colombianos.
    'shipping_estimate' => 280000,

    // Descuento por volumen: porcentaje que aplica desde cierto subtotal.
    'volume_discount' => [
        'from_subtotal' => 2000000,
        'percent' => 8,
    ],

    'quote_valid_days' => 8,

    'max_quantity' => 5000,

    /*
    |--------------------------------------------------------------------------
    | Cobertura
    |--------------------------------------------------------------------------
    */

    // Punto de venta donde el cliente puede recoger su pedido sin pagar transporte.
    'pickup' => env('AGROPOSTE_PICKUP', 'Punto de venta AgroPoste en Ubaté'),

    // AgroPoste atiende solo Ubaté (casco urbano y veredas).
    'zona' => [
        'departamento' => 'Cundinamarca',
        'municipio' => 'Ubaté',
    ],

];
