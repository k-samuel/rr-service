<?php
return [
    'adapter' => \KSamuel\RrService\Connection\Rabbit::class,
    'options' => [
        'connection' => [
            'host' => '',
            'port' => '5672',
            'vhost' => '/',
            'user' => '',
            'password' => ''
        ],
        'definitions' => [
            'exchange_declare' => [         // calls exchange_declare()
                //...
            ],
            'queue_declare' => [            // calls queue_declare()
                //...
            ],
            'queue_bind' => [       // calls queue_bind()
                // format: [queue_name => exchange_name] if value is a string
                // format: [definition] if value is an associative array

            ],
        ]
    ]
];