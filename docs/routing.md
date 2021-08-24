# Service routing and Service Action's

Basic principles of routing in the application.

Service routing is mandatory.

Routing within services is optional.

Service routing is configured in 2 configuration files:

* [config/common/web_routes.php](config/common/web_routes.php)  -  WEB JSON API
* [config/common/cli_routes.php](config/common/cli_routes.php)  -  CLI tasks

## Service
Configuration file example:

```php 
<?php
return [
    // list of routes
    'routes' => [
        // Only specified uri path 
        '/example/api/' => [
            'class' => \App\Example\ExampleService::class
        ],
        // All uri paths starting with  /my-service/complex-actions/ 
        // using KSamuel\RrService\Service\ActionRouter for internal routing 
        '/my-service/complex-actions/*' => [
            'class' => \Service\MyComplexService\Service::class
        ],
    ],
    // роут по умолчанию
    'default_route' => '/example/api/'
];
```

## CLI

Similar to Service routing, the difference is that the configuration file
connects only when launched via CLI


## ActionRouter

Can be used for intra-service routing.

In routing, you need to specify only the uri path part, excluding the uri of the service itself

Example:

```
specific action route: /my-api-name/service-name/my-action
```

In the Action routing settings, specify only `my-action`

Settings example: services/example/config/routes.php
```php
<?php
return [
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
    'default_route' => 'api'
];
```
An example of starting routing within a service :
```php 
// service start
public function run(ServerRequestInterface $req, ResultInterface $resp): void
{
        /**
         * @var App\Service\ActionRouter $actionRouter
         */
        $actionRouter = $this->di->get(App\Service\ActionRouter::class);
        
        $action = $actionRouter->getAction($req, 3); 
        // where 3 - is the ordinal number of the url part with which it starts
        // internal routing of my-action from uri: /my-servise-base/my-service/my-action 
               
        $action->run($req, $resp, $this->di);
}
```
