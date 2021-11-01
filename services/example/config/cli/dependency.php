<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface as c;

return [
    'routes' => static function (c $c) {
        return $c->get(\KSamuel\RrService\Config\Storage::class)->get(__DIR__ . '/routes.php', true);
    },
    // Routing
    \KSamuel\RrService\Service\ActionRouter::class => static function (c $c) {
        return new \KSamuel\RrService\Service\ActionRouter(
            $c->get('routes')
        );
    },
    // PSR-7 response factory
    \KSamuel\RrService\Http\ResponseFactory::class => \KSamuel\RrService\Http\ResponseFactory::class
];
