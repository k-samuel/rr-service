<?php

return [
    // uri path part with service code (other parts routes in service)
    'uri_path_start_index' => 1,
    'services' => [
        'index' => [
            'class' => \App\Example\Action\Index::class
        ],
        'api' => [
            'class' => \App\Example\Action\Api::class
        ],
    ],
    'default_route' => 'index'
];
