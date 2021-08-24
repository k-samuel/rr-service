<?php

declare(strict_types=1);

namespace KSamuel\RrService\Service;

/**
 * Interface ResultInterface
 * Интерфейс системной информации о результатах запуска сервиса
 * @package App\Service
 */
interface ResultInterface
{
    public const RESULT_SUCCESS = 0;
    public const RESULT_ERROR = 1;
    public const RESULT_NO_DATA = 2;

    /**
     * Получить статус результата (константа RESULT_*)
     * @return int
     */
    public function getStatus(): int;

    /**
     * Получить текст ошибки
     * @return string|null
     */
    public function getError(): ?string;

    /**
     * Задать сообщение об ошибке
     * @param string $message
     */
    public function setError(string $message): void;

    /**
     * @return array<string,mixed>
     */
    public function getDebugStat(): array;

    /**
     * Данные которые возвращает сервис
     * @return array<int|string,mixed>
     */
    public function getData(): array;

    /**
     * Установить данные результата обработки запроса
     * @param array<mixed,mixed> $data
     */
    public function setData(array $data): void;

    /**
     * @return int|null
     */
    public function getHttpErrorCode(): ?int;

    /**
     * Задать код ответа при ошибке
     * @param int $code
     */
    public function setHttpErrorCode(int $code): void;
}
