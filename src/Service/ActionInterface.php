<?php

/**
 * @author Kirill Yegorov 2021
 */

declare(strict_types=1);

namespace KSamuel\RrService\Service;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ActionInterface
{
    public function run(ServerRequestInterface $req, ContainerInterface $container): ResponseInterface;
}
