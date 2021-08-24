<?php

declare(strict_types=1);

namespace KSamuel\RrService\Service;

class Result implements ResultInterface
{
    private int $statusCode;

    private ?string $error = null;
    /**
     * @var array<string,mixed>
     */
    private array $debugStat = [];
    /**
     * @var array<int|string,mixed>
     */
    private array $data = [];
    /**
     * HTTP error code
     * @var int|null
     */
    private ?int $httpErrorCode = null;

    public function getStatus(): int
    {
        return $this->statusCode;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getDebugStat(): array
    {
        return $this->debugStat;
    }

    public function setError(string $message): void
    {
        $this->error = $message;
    }

    public function setStatus(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @param array<string,mixed> $data
     */
    public function setDebugStat(array $data): void
    {
        $this->debugStat = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array<mixed,mixed> $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return int|null
     */
    public function getHttpErrorCode(): ?int
    {
        return $this->httpErrorCode;
    }

    /**
     * @param int $code
     */
    public function setHttpErrorCode(int $code): void
    {
        $this->httpErrorCode = $code;
    }

    /**
     *
     */
    public function resetHttpErrorCode(): void
    {
        $this->httpErrorCode = null;
    }
}
