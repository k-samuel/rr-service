<?php
return [
    'adapter' => \Dvelum\Cache\Memcached::class,
    'options' => [
        'compression' => 1,
        'normalizeKeys' => 1,
        'defaultLifeTime' => 604800, // 7 days
        'keyPrefix' => '',
        'persistent_key' => '',
        'servers' => [
            [
                'host' => '',
                'port' => 11211,
                'weight' => 1,
            ]
        ]
    ]
];
