<?php

declare(strict_types=1);

namespace KSamuel\RrService\Config;

class Storage
{
    /**
     * Инициализированные объекты настроек
     * @var Config[]<string,Config>
     */
    private array $configs = [];
    /**
     * Путь к папке с общими настройками
     * @var string
     */
    private string $commonDir;
    /**
     * Путь к папке с настройками в рамках текущего окружения
     * @var string
     */
    private string $configDir;

    /**
     * Инициализировать пути к файлам
     * @param string $commonDir
     * @param string $currentDir
     */
    public function __construct(string $commonDir, string $currentDir)
    {
        $this->commonDir = $commonDir;
        $this->configDir = $currentDir;
    }

    /**
     * Получить путь к папке с настройками текущего окружения
     * @return string
     */
    public function getConfigDir(): string
    {
        return $this->configDir;
    }

    /**
     * Получить файл конфигурации
     * @param string $path
     * @param bool $fullPath - указан полный путь к конфигу, не мержить
     * @return Config
     * @throws \Exception
     */
    public function get(string $path, bool $fullPath = false): Config
    {
        if (!isset($this->configs[$path])) {
            $configData = [];
            $exist = false;

            // Конфиги полный путь
            if ($fullPath) {
                if (file_exists($path)) {
                    $configData = require $path;
                    $exist = true;
                }
                // конфиги относительный путь с мержем
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
                throw new \Exception('Undefined config ' . $path);
            }

            $config = new Config($path);
            $config->setValues($configData);
            $this->configs[$path] = $config;
        }
        return $this->configs[$path];
    }
}
