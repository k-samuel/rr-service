<?php

if (isset($_SERVER['argc']) && $_SERVER['argc'] < 2) {
    exit(1);
}

ini_set('display_errors', 'stderr');
define('ROOT_PATH', __DIR__ . '/');

$config = require ROOT_PATH . 'config/common.php';
include ROOT_PATH . "vendor/autoload.php";

putenv('APPLICATION_CLI=1');
use Nyholm\Psr7;
use KSamuel\RrService\Application;

$psrFactory = new Psr7\Factory\Psr17Factory();
$server = new Application($config);

$request = $psrFactory->createServerRequest('GET', $_SERVER['argv'][1]);
$response = $psrFactory->createResponse(200)->withAddedHeader('Content-Type', 'application/json; charset=utf-8');

try {
    $resp = $server->run($request, $response);
} catch (\Throwable $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(1);
}

echo $response->getBody();

if ($response->getStatusCode() === 200) {
    exit(0);
} else {
    exit(1);
}
