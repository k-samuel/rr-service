<?php

declare(strict_types=1);

namespace KSamuel\RrService\Config;

use InvalidArgumentException;

class Storage
{
    /**
     * Initialized configurations
     * @var Config[]<string,Config>
     */
    private array $configs = [];
    /**
     * Common configs path
     * @var string
     */
    private string $commonDir;
    /**
     * Current environment configs path
     * @var string
     */
    private string $configDir;

    /**
     * @param string $commonDir
     * @param string $currentDir
     */
    public function __construct(string $commonDir, string $currentDir)
    {
        $this->commonDir = $commonDir;
        $this->configDir = $currentDir;
    }

    /**
     * Get path to current environment config dir
     * @return string
     */
    public function getConfigDir(): string
    {
        return $this->configDir;
    }

    /**
     * Get configuration
     * @param string $path
     * @param bool $fullPath - full path to config file, do not use config merge
     * @return Config
     * @throws InvalidArgumentException
     */
    public function get(string $path, bool $fullPath = false): Config
    {
        if (!isset($this->configs[$path])) {
            $configData = [];
            $exist = false;

            // configs, full path
            if ($fullPath) {
                if (file_exists($path)) {
                    $configData = require $path;
                    $exist = true;
                }
                // configs, relative path with merge option
            } else {
                if (file_exists($this->commonDir . $path)) {
                    $configData = require $this->commonDir . $path;
                    $exist = true;
                }

                if (file_exists($this->configDir . $path)) {
                    /**
                     * @var array<string,mixed> $data
                     */
                    $data = require $this->configDir . $path;
                    if (!empty($data)) {
                        foreach ($data as $k => $v) {
                            $configData[$k] = $v;
                        }
                    }
                    $exist = true;
                }
            }

            if (!$exist) {
                throw new InvalidArgumentException('Undefined config ' . $path);
            }

            $config = new Config($path);
            $config->setValues($configData);
            $this->configs[$path] = $config;
        }
        return $this->configs[$path];
    }
}
