<?php

declare(strict_types=1);

namespace App\Example\Action\Cli;

use KSamuel\RrService\Service\ActionInterface;
use KSamuel\RrService\Service\ResultInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Example console worker
 */
class FastWorker implements ActionInterface
{
    /**
     * @param ServerRequestInterface $req
     * @param ResultInterface $res
     * @param ContainerInterface $container
     * @throws \Exception
     */
    public function run(ServerRequestInterface $req, ResultInterface $res, ContainerInterface $container): void
    {
        $result = ['success' => true];
        $res->setData($result);
    }
}
