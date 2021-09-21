<?php
return [
    // uri path part with service code (other parts routes in service)
    'uri_path_start_index' => 0,
    'services' => [
        // all requests starting with /example (php console.php /worker)
        'cli' => [
            'class' => \App\Example\ExampleService::class,
            // path to service dependency config related to service directory
            'dependency' => 'config/cli/dependency.php'
        ],
    ],
    'default_service' => 'cli'
];