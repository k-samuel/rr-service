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

class LoaderContainer implements LoaderInterface
{
    /**
     * @var ServiceInterface[] $services
     */
    private array $services = [];
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
        if (!isset($this->services[$serviceConfig['class']])) {
            $this->services[$serviceConfig['class']] = new $serviceConfig['class'](
                $this->connections,
                $this->log,
                $this->configStore
            );
        }
        return $this->services[$serviceConfig['class']];
    }
}
