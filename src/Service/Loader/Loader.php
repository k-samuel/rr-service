<?php

/**
 * @author Kirill Yegorov 2021
 */

declare(strict_types=1);

namespace KSamuel\RrService\Service\Loader;

use KSamuel\RrService\Config\Storage;
use KSamuel\RrService\Connection\Manager;
use KSamuel\RrService\Service\ServiceInterface;
use Psr\Log\LoggerInterface;

class Loader implements LoaderInterface
{
    private Manager $connections;
    private Storage $configStore;
    private LoggerInterface $log;

    public function __construct(Manager $connections, Storage $configStore, LoggerInterface $log)
    {
        $this->connections = $connections;
        $this->configStore = $configStore;
        $this->log = $log;
    }

    /**
     * @param array<string,mixed> $serviceConfig
     * @return ServiceInterface
     */
    public function loadService(array $serviceConfig): ServiceInterface
    {
        return new $serviceConfig['class']($this->connections, $this->log, $this->configStore);
    }
}
