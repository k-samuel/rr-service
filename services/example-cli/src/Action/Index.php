<?php

declare(strict_types=1);

namespace App\ExampleCli\Action;

use KSamuel\RrService\Service\ActionInterface;
use KSamuel\RrService\Service\ResultInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Example console worker
 */
class Index implements ActionInterface
{
    /**
     * @param ServerRequestInterface $req
     * @param ResultInterface $res
     * @param ContainerInterface $container
     * @throws \Exception
     */
    public function run(ServerRequestInterface $req, ResultInterface $res, ContainerInterface $container): void
    {
        $res->setData(['message' => 'Please select console action']);
    }
}
