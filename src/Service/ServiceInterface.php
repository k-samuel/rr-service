<?php

declare(strict_types=1);

namespace KSamuel\RrService\Service;

use KSamuel\RrService\Config\Storage;
use KSamuel\RrService\Connection;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

/**
 * Interface ServiceInterface
 * Интерфейс описывающий поведение сервисов поиска (стадий)
 * @package App\Service
 * @author Kirill Yegorov 2021
 */
interface ServiceInterface
{
    /**
     * ServiceInterface constructor.
     * @param Connection\Manager $connectionManager
     * @param LoggerInterface $logger
     * @param Storage $configStorage
     */
    public function __construct(Connection\Manager $connectionManager, LoggerInterface $logger, Storage $configStorage);

    /**
     * Инициализация сервиса, прогрев
     */
    public function warmup(): void;

    /**
     * Запуск обработки
     * @param ServerRequestInterface $req - запрос
     * @param ResultInterface $res - контейнер результатов
     */
    public function run(ServerRequestInterface $req, ResultInterface $res): void;
}
