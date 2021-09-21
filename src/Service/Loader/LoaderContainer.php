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
     * @param array{class:string,dependency:string} $serviceConfig
     * @return ServiceInterface
     */
    public function loadService(array $serviceConfig): ServiceInterface
    {
        $key = $serviceConfig['class'] . '::' . $serviceConfig['dependency'];
        if (!isset($this->services[$key])) {
            $this->services[$key] = new $serviceConfig['class'](
                $this->connections,
                $this->log,
                $this->configStore,
                $serviceConfig
            );
        }
        return $this->services[$key];
    }
}
