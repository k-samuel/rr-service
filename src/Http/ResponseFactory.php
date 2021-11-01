<?php

declare(strict_types=1);

namespace KSamuel\RrService\Http;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class ResponseFactory
{
    /**
     * @param array<int|string,mixed> $data
     * @return ResponseInterface
     */
    public function jsonResponse(array $data): ResponseInterface
    {
        return new Response(200, ['Content-Type' => 'application/json; charset=utf-8'], (string)json_encode($data));
    }
}
