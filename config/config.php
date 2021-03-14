<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'token' => env('RECON_TOKEN'),

    'database' => env('RECON_DATABASE', 'default'),

    'queue' => env('RECON_QUEUE', false),
];
