<?php

return [
    'version'     => env('AWS_VERSION', 'latest'),
    'cluster_id'  => env('AWS_REDIS_CLUSTER_ID'),

    /*
    |--------------------------------------------------------------------------
    | ELB Information
    |--------------------------------------------------------------------------
    |
    | The ELB default information
    */
    'region'      => env('AWS_CLOUDWATCH_REGION', 'my-default-region'),
    'credentials' => [
        'key'    => env('AWS_CLOUDWATCH_KEY', 'my-cloudwatch-key'),
        'secret' => env('AWS_CLOUDWATCH_SECRET', 'my-cloudwatch-secret'),
    ],

];