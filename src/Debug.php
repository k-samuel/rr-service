<?php

declare(strict_types=1);

namespace KSamuel\RrService;

use KSamuel\RrService\Connection;

class Debug
{
    /**
     * @var Connection\Manager
     */
    protected $connectionManager;

    /**
     * @var array<string,mixed>
     */
    protected $config;

    /**
     * Debug constructor.
     * @param Connection\Manager $connectionManager
     * @param array<string,mixed> $config
     */
    public function __construct(Connection\Manager $connectionManager, array $config)
    {
        $this->connectionManager = $connectionManager;
        $this->config = $config;
    }

    /**
     * @return array<string,mixed>
     */
    public function getInfo(): array
    {
        $result = [];
        // Retrieving database profiling data
        if ($this->config['sql']) {
            $result['sql'] = $this->sqlInfo();
        }
        // Cache Access Profiling Data
        if ($this->config['cache']) {
            $result['cache'] = $this->cacheInfo();
        }
        return $result;
    }

    /**
     * @return array<string,mixed>
     */
    protected function sqlInfo(): array
    {
        $sqlResult = [
            'count' => 0,
            'time' => 0,
            'query' => []
        ];
        $profilers = $this->connectionManager->getDbProfilers();
        foreach ($profilers as $name => $prof) {
            /**
             * @var \Laminas\Db\Adapter\Profiler\Profiler $prof
             */
            $sqlResult['count'] += count($prof->getProfiles());

            $prof = $prof->getProfiles();
            if (!empty($prof)) {
                foreach ($prof as $item) {
                    $sqlResult['query'][] = [
                        't' => $item['elapse'],
                        'q' => $item['sql'],
                        'c' => $name
                    ];
                    $sqlResult['time'] += $item['elapse'];
                }
            }
        }
        return $sqlResult;
    }

    /**
     * @return array<mixed,mixed>
     */
    protected function cacheInfo(): array
    {
        return $this->connectionManager->getCacheProfilers();
    }
}
