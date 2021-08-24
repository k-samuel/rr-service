How to add new service
===
Creat directory `services/myservice`

Service files structure:

        config/                       // settings
            dependency.php            // DependencyConteiner configuration
            routes.php                // routing settings
        src/                          // code
            Action                    // Actions directory (if needed)  
                Web                   // Web server actions  
                Cli                   // CLI actions  
            MyServiceService.php      // Service class 
        docs/                         // documentation
        readme.md                     // documentation index file


MyServiceService - base class for starting services,
must implement  KSamuel\RrService\Service\ServiceInterface

ServiceInterface::warmup - using for caches warmup and service data initialisation at worker start
For example: adding RuntimeCache for dictionaries and other static application data from external storages

`composer.json` - new services should be registered for autoloading

`config/common/web/routes.php` - basic routes for web server requests
`config/common/cli/routes.php` - basic routes for cli

[Routing](routing.md)

### DI Container
Every service using own DependencyContainer

["Container settings example"](../services/example/config/dependency.php)

Dependency container initializes in service class [ExampleService](../services/example/src/ExampleService.php)

Specialized service configurations should be registered as service dependencies.


### Service routing
Use KSamuel\RrService\Service\ActionRouter to specify service routes
```php 

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
        $container->bind( Config\Storage::class, $configStorage);
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
```
Add routes into services/example/config/routes.php

```php
<?php
return [
    // uri path part with service code (other parts routes in service)
    'uri_path_start_index' => 1,
    'routes' => [
        //============ WEB API =======
        'api' => [
            'class' => \App\Example\Action\Web\Api::class
        ],
        //============== CLI =========
        'cli-worker' => [
            'class' => \App\Example\Action\Cli\Worker::class
        ],
    ],
    // default route
    'default_route' => 'api'
];
```
Action Router should be added into service dependencies configuration

```php
return [
    'routes' => static function (c $c) {
        return $c->get(\KSamuel\RrService\Config\Storage::class)->get(__DIR__ . '/routes.php', true);
    },
    // Routing
    \KSamuel\RrService\Service\ActionRouter::class => static function (c $c) {
        return new \KSamuel\RrService\Service\ActionRouter(
            $c->get('routes')
        );
    }
];
```