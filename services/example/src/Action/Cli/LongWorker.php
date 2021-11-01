<?php

declare(strict_types=1);

namespace App\Example\Action\Cli;

use KSamuel\RrService\Http\ResponseFactory;
use KSamuel\RrService\Service\ActionInterface;
use KSamuel\RrService\Service\ResultInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Example console worker
 */
class LongWorker implements ActionInterface
{
    public function run(ServerRequestInterface $req, ContainerInterface $container): ResponseInterface
    {
        $result = [];
        for ($i = 0; $i < 10; $i++) {
            $result[] = $i;
            sleep(1);
        }

        /**
         * @var ResponseFactory $responseFactory
         */
        $responseFactory = $container->get(ResponseFactory::class);
        $data = [
            'success' => true,
            'result' => $result
        ];
        return $responseFactory->jsonResponse($data);
    }
}
