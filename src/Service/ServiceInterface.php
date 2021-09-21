<?php

declare(strict_types=1);

namespace KSamuel\RrService\Service;

use KSamuel\RrService\Config\Storage;
use KSamuel\RrService\Connection;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

/**
 * Interface ServiceInterface
 * @author Kirill Yegorov 2021
 */
interface ServiceInterface
{
    /**
     * ServiceInterface constructor.
     * @param Connection\Manager $connectionManager
     * @param LoggerInterface $logger
     * @param Storage $configStorage
     * @param array{class:string,dependency:string} $serviceConfig
     */
    public function __construct(Connection\Manager $connectionManager, LoggerInterface $logger, Storage $configStorage, array $serviceConfig);

    /**
     * Warm up service cache, load static dictionaries
     */
    public function warmup(): void;

    /**
     * Request handler
     * @param ServerRequestInterface $req
     * @param ResultInterface $res
     */
    public function run(ServerRequestInterface $req, ResultInterface $res): void;
}
