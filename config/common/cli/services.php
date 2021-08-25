<?php
return [
    // uri path part with service code (other parts routes in service)
    'uri_path_start_index' => 0,
    'services' => [
        // all requests starting with /example (php console.php /worker)
        'worker' => [
            'class' => \App\ExampleCli\ExampleService::class
        ],
    ],
    'default_service' => 'worker'
];