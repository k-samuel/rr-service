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
        $data =  [
            'success' => true,
            'message' => 'Please select worker action',
        ];
        return $responseFactory->jsonResponse($data);
    }
}
