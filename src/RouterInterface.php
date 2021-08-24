<?php

declare(strict_types=1);

namespace KSamuel\RrService;

use KSamuel\RrService\Service\Loader\LoaderInterface;
use KSamuel\RrService\Service\ResultInterface;
use Psr\Http\Message\ServerRequestInterface;

interface RouterInterface
{
    public function route(
        ServerRequestInterface $request,
        LoaderInterface $loader,
        ResultInterface $result
    ): ResultInterface;
}
