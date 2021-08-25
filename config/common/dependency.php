<?php
declare(strict_types=1);

use Psr\Container\ContainerInterface as c;

return [

    /**
     * @todo Change to your implementation
     */
    Psr\Log\LoggerInterface::class => static function (c $c) {
        return new \KSamuel\RrService\Logger\ExampleFakeLogger();
    },

    'application.mode' => static function (c $c) {
        if (getenv('APPLICATION_CLI') === '1') {
            return KSamuel\RrService\Application::MODE_CLI;
        } else {
            return KSamuel\RrService\Application::MODE_WEB;
        }
    },

    'server.config' => static function (c $c) {
        /**
         * @var KSamuel\RrService\Config\Storage $storage
         */
        $storage = $c->get(KSamuel\RrService\Config\Storage::class);
        return $storage->get('server.php');
    },

    'router.config' => static function (c $c) {
        /**
         * @var KSamuel\RrService\Config\Storage $storage
         */
        $storage = $c->get(KSamuel\RrService\Config\Storage::class);
        if ($c->get('application.mode') === KSamuel\RrService\Application::MODE_CLI) {
            return $storage->get('cli/services.php')->__toArray();
        } else {
            return $storage->get('web/services.php')->__toArray();
        }
    },

    'config.dir' => static function (c $c) {
        /**
         * @var KSamuel\RrService\Config\Storage $storage
         */
        $storage = $c->get(KSamuel\RrService\Config\Storage::class);
        return $storage->getConfigDir();
    },

    // External connections
    KSamuel\RrService\Connection\Manager::class => static function (c $c) {
        $configDir = $c->get('config.dir');
        /**
         * @var KSamuel\RrService\Config\Config $serverConfig
         */
        $serverConfig = $c->get('server.config');
        $config = new KSamuel\RrService\Config\Config('connections');
        $config->setValues(
            [
                'paths' => [
                    'cache' => $configDir . 'connection/cache/',
                    'db' => $configDir . 'connection/db/',
                    'mongo' => $configDir . 'connection/mongo/',
                    'rabbit' => $configDir . 'connection/rabbit/',
                    'sphinx' => $configDir . 'connection/sphinx/',
                ],
                'debug' => $serverConfig->get('debug'),
                'debug_options' => $serverConfig->get('debug_options'),
            ]
        );
        return new KSamuel\RrService\Connection\Manager($config);
    },

    // Service loader
    KSamuel\RrService\Service\Loader\LoaderInterface::class => static function (c $c) {
        if ($c->get('application.mode') === KSamuel\RrService\Application::MODE_WEB) {
            return new KSamuel\RrService\Service\Loader\LoaderContainer(
                $c->get(KSamuel\RrService\Connection\Manager::class),
                $c->get(KSamuel\RrService\Config\Storage::class),
                $c->get(\Psr\Log\LoggerInterface::class)
            );
        }
        return new KSamuel\RrService\Service\Loader\Loader(
            $c->get(KSamuel\RrService\Connection\Manager::class),
            $c->get(KSamuel\RrService\Config\Storage::class),
            $c->get(Psr\Log\LoggerInterface::class)
        );
    },

    // Routing helper
    KSamuel\RrService\Uri::class => KSamuel\RrService\Uri::class,

    // Base mapping and routing for requests
    KSamuel\RrService\RouterInterface::class => static function (c $c) {
        return new KSamuel\RrService\Router($c->get('router.config'), $c->get(KSamuel\RrService\Uri::class));
    },
];