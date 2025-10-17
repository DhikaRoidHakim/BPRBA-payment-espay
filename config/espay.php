<?php

use Illuminate\Support\Env;

return [


    /*
    |--------------------------------------------------------------------------
    | Espay Configuration
    |--------------------------------------------------------------------------
    |
    | 
    |
    */

    'signature_key' => env('ESPAY_SIGNATURE_CODE'),
    'merchant_code' => env('ESPAY_MERCHANT_CODE'),
];
