<?php

declare(strict_types=1);

namespace KSamuel\RrService\Service;

/**
 * Interface ResultInterface
 * Service results
 * @package App\Service
 */
interface ResultInterface
{
    public const RESULT_SUCCESS = 0;
    public const RESULT_ERROR = 1;
    public const RESULT_NO_DATA = 2;

    /**
     * Get result status (constant RESULT_*)
     * @return int
     */
    public function getStatus(): int;

    /**
     * Get error message
     * @return string|null
     */
    public function getError(): ?string;

    /**
     * Set error message
     * @param string $message
     */
    public function setError(string $message): void;

    /**
     * Get debug statistics
     * @return array<string,mixed>
     */
    public function getDebugStat(): array;

    /**
     * Get service result data
     * @return array<int|string,mixed>
     */
    public function getData(): array;

    /**
     * Set result data
     * @param array<mixed,mixed> $data
     */
    public function setData(array $data): void;

    /**
     * Get http error cod
     * @return int|null
     */
    public function getHttpErrorCode(): ?int;

    /**
     * Set http error code
     * @param int $code
     */
    public function setHttpErrorCode(int $code): void;
}
