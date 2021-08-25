<?php

declare(strict_types=1);

namespace KSamuel\RrService\Config;

use InvalidArgumentException;

class Config
{
    /**
     * @var string
     */
    protected string $name;
    /**
     * @var array<string,mixed>
     */
    protected array $data;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param array<string,mixed> $data
     */
    public function setValues(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @param string $key
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function get(string $key)
    {
        if (!isset($this->data[$key])) {
            throw new InvalidArgumentException('Undefined config key ' . $this->name . '::' . $key);
        }
        return $this->data[$key];
    }

    /**
     * @return array<string,mixed>
     */
    public function __toArray(): array
    {
        return $this->data;
    }
}
