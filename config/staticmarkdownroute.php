<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Static Site generation
    |--------------------------------------------------------------------------
    |
    | This option controls the default cache connection that gets used while
    | using this caching library. This connection is used when another is
    | not explicitly specified when executing a given caching function
    |
    */

    'enabled' => env('SMR_ENABLED', true),
    'force' => env('SMR_FORCE', true),

];
