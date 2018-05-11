<?php

return [
    'version'         => env('AWS_ELB_VERSION', 'latest'),

    /*
    |--------------------------------------------------------------------------
    | ELB Information
    |--------------------------------------------------------------------------
    |
    | The ELB default information
    */
    'name'        => env('AWS_ELB_NAME', 'my-default-elb-name'),
    'region'      => env('AWS_ELB_REGION', 'my-default-region'),
    'credentials' => [
        'key'    => env('AWS_ELB_KEY', 'my-elb-key'),
        'secret' => env('AWS_ELB_SECRET', 'my-elb-secret'),
    ],

];