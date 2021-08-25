<?php

namespace KSamuel\RrService\Service;

use Psr\Container\ContainerInterface;
use RuntimeException;

/**
 * Runtime dependency container
 * @package App\Service
 */
class DependencyContainer implements ContainerInterface
{
    /**
     * @var array<string,Object>
     */
    protected array $container;

    /**
     * @param string $interfaceName
     * @param object $object
     */
    public function bind(string $interfaceName, object $object): void
    {
        if (!$object instanceof $interfaceName) {
            throw new RuntimeException('DC: ' . get_class($object) . ' is not instance of ' . $interfaceName);
        }
        $this->container[$interfaceName] = $object;
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function get(string $id)
    {
        if (!isset($this->container[$id])) {
            throw new RuntimeException('Unresolved runtime dependency ' . $id);
        }
        return $this->container[$id];
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->container[$id]);
    }

    /**
     * @param array<string,mixed> $config
     */
    public function bindArray(array $config): void
    {
        foreach ($config as $id => $object) {
            if (is_callable($object)) {
                $this->container[$id] = $object($this);
            } elseif (is_object($object)) {
                $this->container[$id] = $object;
            } elseif (is_string($object)) {
                $this->container[$id] = new $object();
            }
        }
    }

    public function bindPhpConfig(string $filepath): void
    {
        if (!file_exists($filepath)) {
            throw new RuntimeException('Undefined DI config ' . $filepath);
        }
        $config = require $filepath;

        if (!is_array($config) || empty($config)) {
            throw new RuntimeException('Empty DI config ' . $filepath);
        }

        $this->bindArray($config);
    }
}
