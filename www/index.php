<?php

/*
 * Run application using web server (nginx, apache)
 */

define('ROOT_PATH', dirname(__FILE__,2) . '/');
$config = require ROOT_PATH . 'config/common.php';

error_reporting(E_ALL);
$time = microtime(true);
chdir('../');

include ROOT_PATH . "vendor/autoload.php";

use KSamuel\RrService\Pinba;
use KSamuel\RrService\Application;

$server = new Application($config);
$server->warmUp();

$psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();
$creator = new \Nyholm\Psr7Server\ServerRequestCreator(
    $psr17Factory, // ServerRequestFactory
    $psr17Factory, // UriFactory
    $psr17Factory, // UploadedFileFactory
    $psr17Factory  // StreamFactory
);
$serverRequest = $creator->fromGlobals();
$response = $psr17Factory->createResponse(200)->withAddedHeader('Content-Type', 'application/json; charset=utf-8');
$resp = $server->run($serverRequest , $response);
(new \Laminas\HttpHandlerRunner\Emitter\SapiEmitter())->emit($resp);
