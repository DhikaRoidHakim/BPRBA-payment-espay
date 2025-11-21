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

    // Development 
    'signature_key' => env('ESPAY_SIGNATURE_CODE_DEVELOPMENT'),
    'merchant_code' => env('ESPAY_MERCHANT_CODE_DEVELOPMENT'),

    // Production
    // 'signature_key' => env('ESPAY_SIGNATURE_CODE_PRODUCTION'),
    // 'merchant_code' => env('ESPAY_MERCHANT_CODE_PRODUCTION'),
];
