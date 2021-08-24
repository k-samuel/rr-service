<?php
$baseDir = ROOT_PATH;
return [
    // config path for common settings
    'config_common' => $baseDir . 'config/common/',
    // config path for current environment (dev/prod)
    'config_path' => $baseDir . 'config/dev/',
    // Pinba server
    'pinba_server' => 'personal_offers_service',
    // Memcached session server (if needed)
    'memcached_session_server' => '127.0.0.1:11211',
    // TTL for runtime cache
    'runtime_cache_lifetime' => 60 * 30,
];