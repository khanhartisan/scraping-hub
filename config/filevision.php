<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default FileVision Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default FileVision driver that will be used
    | by the application. The driver specified here will be used when no
    | explicit driver is specified when analyzing files.
    |
    */

    'default' => env('FILEVISION_DRIVER', 'basic'),

    /*
    |--------------------------------------------------------------------------
    | FileVision Drivers
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection options for every FileVision
    | driver used by your application. An example configuration is provided
    | for each driver supported. You're also free to add more drivers.
    |
    | Supported drivers: "basic", "openai"
    |
    */

    'drivers' => [

        'basic' => [
            // Basic driver uses file system information only
        ],

        'openai' => [
            'model' => env('FILEVISION_OPENAI_MODEL', 'gpt-4o-mini'),
        ],

    ],

];
