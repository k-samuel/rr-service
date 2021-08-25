# Service routing and Service Action's

Basic principles of routing in the application.

Service routing is mandatory.

Routing within services is optional.

Service routing is configured in 2 configuration files:

* [config/common/web/services.php](../config/common/web/services.php)  -  WEB JSON API
* [config/common/cli/services.php](../config/common/cli/services.php)  -  CLI tasks

## Service
Configuration file example:

```php 
<?php
return [
    // uri path part with service code (other parts routes in service)
    'uri_path_start_index' => 0,
    'services' => [
        // All requests starting with /example/
        'example' => [
            'class' => \App\Example\ExampleService::class
        ],
    ],
    'default_service' => 'example'
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
specific action route: /example/api
```

In the Action routing settings, specify only `api`

Settings example: services/example/config/routes.php
```php
<?php
return [
    // uri path part with service code /example/[1]/ 
    'uri_path_start_index' => 1,
    'services' => [
        'api' => [
            'class' => \App\Example\Action\Web\Api::class
        ],
        'index' => [
            'class' => \App\Example\Action\Web\Index::class
        ],
    ],
    'default_route' => 'index'
];
```
An example of starting routing within a service :
```php 
// service start
public function run(ServerRequestInterface $req, ResultInterface $resp): void
{
        /**
         * @var ActionRouter $actionRouter
         */
        $actionRouter = $this->di->get(ActionRouter::class);
        $action = $actionRouter->getAction($req);
        $action->run($req, $res, $this->di);
}
```
