<?php

declare(strict_types=1);

namespace App\Example\Action\Web;

use KSamuel\RrService\Service\ActionInterface;
use KSamuel\RrService\Service\ResultInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Example action for web API
 */
class Api implements ActionInterface
{
    /**
     * @param ServerRequestInterface $req
     * @param ResultInterface $res
     * @param ContainerInterface $container
     * @throws \Exception
     */
    public function run(ServerRequestInterface $req, ResultInterface $res, ContainerInterface $container): void
    {
        $res->setData(
            [
                'success' => true,
                'message' => 'Example action',
                'data' => [
                    'field1' => random_int(0, 100),
                    'field2' => random_int(100, 1000)
                ]
            ]
        );
    }
}
