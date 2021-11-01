<?php
return [
    'adapter' => \KSamuel\RrService\Connection\Sphinx::class,
    'options' => [
        'host' => '',
        'port' =>  9306,
        'query_time' => 2
    ]
];