<?php

declare(strict_types=1);

namespace App\Example;

use KSamuel\RrService\Config;
use KSamuel\RrService\Connection;
use KSamuel\RrService\Service\ActionRouter;
use KSamuel\RrService\Service\DependencyContainer;
use KSamuel\RrService\Service\ResultInterface;
use KSamuel\RrService\Service\ServiceInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class ExampleService implements ServiceInterface
{
    private ContainerInterface $di;

    public function __construct(
        Connection\Manager $connectionManager,
        LoggerInterface $logger,
        Config\Storage $configStorage
    ) {
        $container = new DependencyContainer();
        $container->bind(LoggerInterface::class, $logger);
        $container->bind(Connection\Manager::class, $connectionManager);
        $container->bind(Config\Storage::class, $configStorage);
        $container->bindPhpConfig(dirname(__DIR__) . '/config/dependency.php');

        $this->di = $container;
    }

    public function warmup(): void
    {
        // worm up cache
    }

    public function run(ServerRequestInterface $req, ResultInterface $res): void
    {
        /**
         * @var ActionRouter $actionRouter
         */
        $actionRouter = $this->di->get(ActionRouter::class);
        $action = $actionRouter->getAction($req);
        $action->run($req, $res, $this->di);
    }
}