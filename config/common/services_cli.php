<?php
return [
    // uri path part with service code (other parts routes in service)
    'uri_path_start_index' => 0,
    'services' => [
        'example' => [
            'class' => \App\Example\ExampleService::class
        ],
    ],
    'default_service' => 'example'
];