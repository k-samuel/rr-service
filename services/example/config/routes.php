<?php

return [
    // uri path part with service code (other parts routes in service)
    'uri_path_start_index' => 1,
    'routes' => [
        //============ WEB API =======
        'api' => [
            'class' => \App\Example\Action\Web\Api::class
        ],
        //============== CLI =========
        'cli-worker' => [
            'class' => \App\Example\Action\Cli\Worker::class
        ],
    ],
    'default_route' => 'api'
];
