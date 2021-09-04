<?php

/*
 * Run application using Road runner
 */

ini_set('display_errors', 'stderr');
define('ROOT_PATH', __DIR__ . '/');

$config = require ROOT_PATH . 'config/common.php';

//memcache session
// ini_set('session.save_handler', 'memcached');
// ini_set('session.save_path', $config['memcached_session_server']);
// ini_set('session.use_only_cookies', false);
// ini_set('session.use_cookies', false);
// ini_set('session.use_trans_sid', false);
// ini_set('session.cache_limiter', null);

// ini_set('pinba.auto_flush', false);

include ROOT_PATH . "vendor/autoload.php";

use Spiral\RoadRunner;
use Nyholm\Psr7;
use KSamuel\RrService\Pinba;
use KSamuel\RrService\Application;

// hide warmup from Pinba stats
Pinba::setServer('unknown');
Pinba::start('warmUp');

$worker = RoadRunner\Worker::create();
$psrFactory = new Psr7\Factory\Psr17Factory();

$worker = new RoadRunner\Http\PSR7Worker($worker, $psrFactory, $psrFactory, $psrFactory);

$server = new Application($config);
$server->warmUp();

Pinba::stop('warmUp');
Pinba::flush('worker.php init');

// reset external connection every request
$server->resetConnections();

/**
 * Last Runtime Cache reset time
 * @var int $runtimeResetTime
 */
$runtimeResetTime = time();
$config['runtime_cache_lifetime'] += rand(0, 5 * 60);

while ($req = $worker->waitRequest()) {
    //dump wait metrics to unknown server
    Pinba::flush('worker.php wait');
    Pinba::setServer($config['pinba_server']);
    try {
        $response = $psrFactory->createResponse(200)->withAddedHeader(
            'Content-Type',
            'application/json; charset=utf-8'
        );
        $resp = $server->run($req, $response);
        $worker->respond($resp);
    } catch (\Throwable $e) {
        $worker->getWorker()->error($e->getMessage() . ' ' . $e->getTraceAsString());
    }

    //move metrics to actual server
    Pinba::flush($req->getUri()->getPath() . '?' . http_build_query($req->getQueryParams()));
    Pinba::setServer('unknown');

    // reset Runtime Cache
    if ($runtimeResetTime + $config['runtime_cache_lifetime'] < time()) {
        $result = $server->resetRuntimeCache();
        $server->warmUp();
        $runtimeResetTime = time();
    }
    // reset connections
    $server->resetConnections();
}