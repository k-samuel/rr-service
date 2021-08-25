<?php

declare(strict_types=1);

namespace KSamuel\RrService;

class Uri
{
    /**
     * @param string $uri
     * @param int $index  - indexes from 0
     * @param string|null $default
     * @return string|null
     */
    public function getPart(string $uri, int $index, ?string $default = null): ?string
    {
        $uri = trim($uri, '/');
        $parts = explode('/', trim($uri, '/'));

        if (isset($parts[$index])) {
            return (string)$parts[$index];
        }

        return $default ?? null;
    }
}
