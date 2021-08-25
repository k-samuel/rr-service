<?php

return [
    // uri path part with service code (other parts routes in service)
    'uri_path_start_index' => 0,
    'services' => [
        'worker' => [
            'class' => \App\ExampleCli\Action\Worker::class
        ],
        'index' => [
            'class' => \App\ExampleCli\Action\Index::class
        ]
    ],
    'default_route' => 'index'
];
