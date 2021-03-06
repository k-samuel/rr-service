<?php

declare(strict_types=1);

namespace App\Example;

use KSamuel\RrService\Config;
use KSamuel\RrService\Connection;
use KSamuel\RrService\Http\ResponseFactory;
use KSamuel\RrService\Service\ActionRouter;
use KSamuel\RrService\Service\DependencyContainer;
use KSamuel\RrService\Service\ResultInterface;
use KSamuel\RrService\Service\ServiceInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class ExampleService implements ServiceInterface
{
    private ContainerInterface $di;

    public function __construct(
        Connection\Manager $connectionManager,
        LoggerInterface $logger,
        Config\Storage $configStorage,
        array $serviceConfig
    ) {
        $container = new DependencyContainer();
        $container->bind(LoggerInterface::class, $logger);
        $container->bind(Connection\Manager::class, $connectionManager);
        $container->bind(Config\Storage::class, $configStorage);

        if (!isset($serviceConfig['dependency'])) {
            throw new \InvalidArgumentException('Undefined service dependency config');
        }

        $container->bindPhpConfig(dirname(__DIR__) . '/' . $serviceConfig['dependency']);

        $this->di = $container;
    }

    public function warmup(): void
    {
        // worm up cache
    }

    public function run(ServerRequestInterface $req): ResponseInterface
    {
        /**
         * @var ActionRouter $actionRouter
         */
        $actionRouter = $this->di->get(ActionRouter::class);
        $action = $actionRouter->getAction($req);
        return $action->run($req, $this->di);
    }
}
