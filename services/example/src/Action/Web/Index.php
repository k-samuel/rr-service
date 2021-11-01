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
class Index implements ActionInterface
{
    public function run(ServerRequestInterface $req, ContainerInterface $container): ResponseInterface
    {
        /**
         * @var ResponseFactory $responseFactory
         */
        $responseFactory = $container->get(ResponseFactory::class);
        $data = [
            'success' => true,
            'message' => 'Index action',
        ];
        return $responseFactory->jsonResponse($data);
    }
}
