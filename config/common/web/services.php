<?php
return [
    // uri path part with service code (other parts routes in service)
    'uri_path_start_index' => 1,
    'services' => [
        // all requests starting with /example (http:://my.host/example/api)
        'example' => [
            'class' => \App\Example\ExampleService::class
        ],
    ],
    'default_service' => 'example'
];
