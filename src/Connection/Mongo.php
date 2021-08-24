<?php

declare(strict_types=1);

namespace KSamuel\RrService\Connection;

use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Manager;
use MongoDB\Driver\WriteConcern;

/**
 * Class Mongo
 * @package App\Connection
 */
class Mongo implements ConnectionInterface
{
    /**
     * @var array<string,mixed>
     */
    protected $config = [
        'write_acknowledge' => 0,
        'write_timeout' => 1000,
        'write_journal' => false,
        'write_ordered' => false
    ];
    /**
     * @var Manager
     */
    protected $manager;
    /**
     * @var WriteConcern
     */
    protected $concern;

    /**
     * @param array<string,mixed> $config
     */
    public function __construct($config)
    {
        foreach ($config as $k => $v) {
            $this->config[$k] = $v;
        }

        $this->concern = new WriteConcern(
            $this->config['write_acknowledge'],
            $this->config['write_timeout'],
            $this->config['write_journal']
        );
    }

    /**
     * @param string $collection
     * @param array<mixed,mixed> $data
     * @param int $size
     * @return bool
     */
    public function insert($collection, $data, $size = 50)
    {
        if (!isset($this->manager)) {
            $this->connect();
        }

        $bulkResult = new BulkWrite(['ordered' => (bool)$this->config['write_ordered']]);
        $count = 0;
        foreach ($data as $row) {
            $bulkResult->insert($row);
            if ($count >= $size) {
                $this->manager->executeBulkWrite($collection, $bulkResult, $this->concern);
                $bulkResult = new BulkWrite(['ordered' => (bool)$this->config['write_ordered']]);
            }
            $count++;
        }
        if ($bulkResult->count() > 0) {
            $this->manager->executeBulkWrite($collection, $bulkResult, $this->concern);
        }
        return true;
    }

    /**
     * @return null
     */
    public function getProfiler()
    {
        return null;
    }

    /**
     */
    public function close(): void
    {
        unset($this->manager);
    }

    /**
     * Compatibility hack
     * @return void
     */
    public function connect()
    {
        $this->manager = new Manager($this->config['uri']);
    }
}
