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
        'qos' => [
            'prefetch_size' => null,
            'prefetch_count' => 1,
            'a_global' => null,
        ],
        'definitions' => [
            'exchange_declare' => [         // calls exchange_declare()
                'SomeExchange.direct' => [
                    /*
                    'name' => 'SomeExchange.direct',
                    'type' => 'direct',     // string - may be 'direct', 'fanout', 'topic', or 'headers'
                    'passive' => false,     // do not create exchange, just check
                    'durable' => true,      // keep exchange after server restart
                    'auto_delete' => false, // If set, the exchange is deleted when all queues have finished using it.
                    */
                ],
            ],
            'queue_declare' => [            // calls queue_declare()
                /*
                'someQueue' => [
                    'name' => 'someQueue',
                    'passive' => false,     // do not create queue, just check
                    'durable' => true,      // keep queue after server restart
                    'exclusive' => false,   // accessible only for current connection, delete after disconnect
                    'auto_delete' => false,  // If set, the queue is deleted when all consumers have finished using it.
                    'nowait' => false,      // do not send a reply method
                    'arguments'=> [
                        'x-message-ttl' => 60*60,     // message time to live
                        'x-max-length' => 25000,      // max queue messages
                        'x-overflow' => 'drop-head'   // 'drop-head' (default) or 'reject-publish'
                    ]
                ],
                */
            ],
            'queue_bind' => [       // calls queue_bind()
                // format: [queue_name => exchange_name] if value is a string
                // format: [definition] if value is an associative array
                /*
                'someQueue' => 'SomeExchange.direct',
                */
            ],
        ]
    ]
];