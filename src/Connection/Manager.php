<?php

declare(strict_types=1);

namespace KSamuel\RrService\Connection;

use Dvelum\Cache;
use Dvelum\Cache\CacheInterface;
use Dvelum\Cache\Runtime;
use Dvelum\Db;
use KSamuel\RrService\Config\Config;

class Manager
{
    /**
     * @var array <string, ConnectionInterface>
     */
    protected $connections = [];

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Cache\CacheInterface|null
     */
    protected $runtime;

    /**
     * @var bool
     */
    protected $debugEnabled;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        if ($this->config->get('debug')) {
            $this->debugEnabled = true;
        }
    }

    /**
     * @return void
     */
    public function resetConnections(): void
    {
        foreach ($this->connections as $item) {
            $item->close();
        }
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    public function getConnectionConfig(string $type, string $name)
    {
        if (!isset($this->config->get('paths')[$type])) {
            throw new \Exception('Unknown type ' . $type);
        }
        $path = $this->config->get('paths')[$type];

        $fullPath = $path . $name . '.php';
        if (!file_exists($fullPath)) {
            throw new \Exception('Could not locate connection config ' . $fullPath);
        }
        $config = require $fullPath;
        if ($this->debugEnabled) {
            $config['options']['profiler'] = true;
        }
        if (empty($config)) {
            throw new \Exception('Empty connection config ' . $fullPath);
        }
        return $config;
    }

    /**
     * Получить кэш уровня приложения
     * @return CacheInterface
     */
    public function getRuntimeCache(): CacheInterface
    {
        if (is_null($this->runtime)) {
            $this->runtime = new Runtime();
        }
        return $this->runtime;
    }

    /**
     * @param string $type
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    protected function getConnection(string $type, string $name)
    {
        $index = $type . '-' . $name;
        if (!isset($this->connections[$index])) {
            $config = $this->getConnectionConfig($type, $name);
            $adapterName = $config['adapter'];
            $this->connections[$index] = new $adapterName($config['options']);
        }
        return $this->connections[$index];
    }

    /**
     * @param string $name
     * @return CacheInterface
     * @throws \Exception
     */
    public function getCache(string $name): CacheInterface
    {
        $connection = $this->getConnection('cache', $name);
        if (!($connection instanceof CacheInterface)) {
            throw new \Exception('Wrong adapter type for connection ' . $name);
        }
        return $connection;
    }

    /**
     * @param string $name
     * @return Db\Adapter
     * @throws \Exception
     */
    public function getDb(string $name): Db\Adapter
    {
        $connection = $this->getConnection('db', $name);
        if (!($connection instanceof Db\Adapter)) {
            throw new \Exception('Wrong adapter type for connection ' . $name);
        }
        return $connection;
    }

    /**
     * @param string $name
     * @return Mongo
     * @throws \Exception
     */
    public function getMongoConnection(string $name): Mongo
    {
        $connection = $this->getConnection('mongo', $name);
        if (!($connection instanceof Mongo)) {
            throw new \Exception('Wrong adapter type for connection ' . $name);
        }
        return $connection;
    }

    /**
     * @param string $name
     * @return Rabbit
     * @throws \Exception
     */
    public function getRabbitConnection(string $name): Rabbit
    {
        $connection = $this->getConnection('rabbit', $name);
        if (!($connection instanceof Rabbit)) {
            throw new \Exception('Wrong adapter type for connection ' . $name);
        }
        return $connection;
    }

    public function getSphinxConnection(string $name): Sphinx
    {
        $connection = $this->getConnection('sphinx', $name);
        if (!($connection instanceof Sphinx)) {
            throw new \Exception('Wrong adapter type for connection ' . $name);
        }
        return $connection;
    }

    /**
     * Получить профили запросов к бд
     * @return array<string,mixed>
     */
    public function getDbProfilers(): array
    {
        $result = [];
        foreach ($this->connections as $name => $conn) {
            if ($conn instanceof Db\Adapter) {
                $prof = $conn->getProfiler();
                if ($prof !== null) {
                    $result[$name] = $prof;
                }
            }
        }
        return $result;
    }

    /**
     * Получить профили запросов к кэшу
     * @return array<string,mixed>
     */
    public function getCacheProfilers(): array
    {
        $result = [];

        foreach ($this->connections as $name => $adapter) {
            if ($adapter instanceof CacheInterface) {
                $result[$name] = $adapter->getOperationsStat();
            }
        }

        if ($this->runtime) {
            $result['runtime'] = $this->runtime->getOperationsStat();
        }

        return $result;
    }

    /**
     * Reset debug profiles
     */
    public function resetProfiles(): void
    {
        foreach ($this->connections as $adapter) {
            if ($adapter instanceof CacheInterface) {
                $adapter->resetOperationsStat();
            }
        }

        if ($this->runtime) {
            $this->runtime->resetOperationsStat();
        }
    }

    /**
     * Check if debug profiling is enabled
     * @return bool
     */
    public function isDebugEnabled(): bool
    {
        return $this->debugEnabled;
    }
}
