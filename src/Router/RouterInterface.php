<?php

declare(strict_types=1);

namespace KSamuel\RrService\Router;

use KSamuel\RrService\Service\Loader\LoaderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface RouterInterface
{
    public function route(
        ServerRequestInterface $request,
        LoaderInterface $loader
    ): ResponseInterface;
}
