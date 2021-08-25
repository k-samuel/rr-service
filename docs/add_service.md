How to add new service
===
Creat directory `services/example`

Service files structure:

        config/                       // settings
            dependency.php            // DependencyConteiner configuration
            routes.php                // routing settings
        src/                          // code
            Action                    // Actions directory (if needed)
            ExampleService.php      // Service class 
        docs/                         // documentation
        readme.md                     // documentation index file


%ExampleService% - base class for starting services, 
must implement  ```KSamuel\RrService\Service\ServiceInterface```

ServiceInterface::warmup - using for caches warmup and service data initialisation at worker start
For example: adding RuntimeCache for dictionaries and other static application data from external storages

`composer.json` - new services should be registered in autoload section

`config/common/web/services.php` - basic routes for web server requests

`config/common/cli/services.php` - basic routes for cli

[Routing documentation](routing.md)

### DI Container
Every service using own DependencyContainer. [ExampleService](../services/example/src/ExampleService.php)

Specialized service configurations should be registered as service dependencies. [Example dependencies](../services/example/config/dependency.php)


### Service routing
Use ```KSamuel\RrService\Service\ActionRouter``` to specify service routes

[ExampleService](../services/example/src/ExampleService.php)

[Example routes](../services/example/config/routes.php)

Action Router should be added into [Example dependencies](../services/example/config/dependency.php)
