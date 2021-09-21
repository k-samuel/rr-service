<?php

return [
    // uri path part with service code (other parts routes in service)
    'uri_path_start_index' => 0,
    'services' => [
        'worker1' => [
            'class' => \App\Example\Action\Cli\LongWorker::class
        ],
        'worker2' => [
            'class' => \App\Example\Action\Cli\FastWorker::class
        ],
        'index' => [
            'class' => \App\Example\Action\Cli\Index::class
        ]
    ],
    'default_route' => 'index'
];