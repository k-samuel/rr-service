<?php

declare(strict_types=1);

namespace App\Example\Action\Web;

use KSamuel\RrService\Http\ResponseFactory;
use KSamuel\RrService\Service\ActionInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Example action for web API
 */
class Api implements ActionInterface
{
    public function run(ServerRequestInterface $req, ContainerInterface $container): ResponseInterface
    {
        /**
         * @var ResponseFactory $responseFactory
         */
        $responseFactory = $container->get(ResponseFactory::class);
        $data = [
            'success' => true,
            'message' => 'Example action',
            'data' => [
                'field1' => random_int(0, 100),
                'field2' => random_int(100, 1000)
            ]
        ];
        return $responseFactory->jsonResponse($data);
    }
}
