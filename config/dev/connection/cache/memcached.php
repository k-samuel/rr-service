<?php
return [
    'adapter' => \Dvelum\Cache\Memcached::class,
    'options' => [
        'compression' => 1,
        'normalizeKeys' => 1,
        'defaultLifeTime' => 604800, // 7 days
        'keyPrefix' => 'prefix_key',
        'persistent_key' => 'prefix_key_cache',
        'servers' => [
            [
                'host' => 'localhost',
                'port' => 11211,
                'weight' => 1,
            ]
        ]
    ]
];
