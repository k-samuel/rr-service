<?php

return [
    // uri path part with service code (other parts routes in service)
    'uri_path_start_index' => 1,
    'services' => [
        'index' => [
            'class' => \App\Example\Action\Web\Index::class
        ],
        'api' => [
            'class' => \App\Example\Action\Web\Api::class
        ],
    ],
    'default_route' => 'index'
];
