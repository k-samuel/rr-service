<?php

/*
 * Run application using Road runner
 */

ini_set('display_errors', 'stderr');
define('ROOT_PATH', __DIR__ . '/');

$config = require ROOT_PATH . 'config/common.php';

include ROOT_PATH . "vendor/autoload.php";

use Spiral\RoadRunner;
use Nyholm\Psr7;
use KSamuel\RrService\Application;

$worker = RoadRunner\Worker::create();
$psrFactory = new Psr7\Factory\Psr17Factory();

$worker = new RoadRunner\Http\PSR7Worker($worker, $psrFactory, $psrFactory, $psrFactory);

$server = new Application($config);
$server->warmUp();

// reset external connection every request
$server->resetConnections();

/**
 * Last Runtime Cache reset time
 */
$runtimeResetTime = time();
$config['runtime_cache_lifetime'] += random_int(1, 5 * 60);

while ($req = $worker->waitRequest()) {
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

    // reset Runtime Cache
    if ($runtimeResetTime + $config['runtime_cache_lifetime'] < time()) {
        $result = $server->resetRuntimeCache();
        $server->warmUp();
        $runtimeResetTime = time();
    }
    // reset connections
    $server->resetConnections();
}