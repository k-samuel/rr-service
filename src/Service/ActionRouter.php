<?php

declare(strict_types=1);

namespace KSamuel\RrService\Service;

use KSamuel\RrService\Config\Config;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author Kirill Yegorov 2021
 */
class ActionRouter
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param ServerRequestInterface $req
     * @return ActionInterface
     * @throws \Exception
     */
    public function getAction(ServerRequestInterface $req): ActionInterface
    {
        $routeStartIndex = $this->config->get('uri_path_start_index');
        $routes = $this->config->get('services');

        $uri = $req->getUri()->getPath();
        $parts = explode('/', trim($uri, '/'));
        $routeParts = [];
        foreach ($parts as $index => $value) {
            if ($index >= $routeStartIndex) {
                $routeParts[] = $value;
            }
        }
        $action = '';
        if (!empty($routeParts)) {
            $action = implode('/', $routeParts);
        }

        if (array_key_exists($action, $routes)) {
            $routeConfig = $routes[$action];
        } else {
            if (!isset($routes[$this->config->get('default_route')])) {
                throw new \RuntimeException('undefined default action route ' . $this->config->get('default_route'));
            }
            $routeConfig = $routes[$this->config->get('default_route')];
        }

        $action = new $routeConfig['class']();
        if (!$action instanceof ActionInterface) {
            throw new \RuntimeException($routeConfig['class'] . ' should implement ' . ActionInterface::class);
        }
        return new $routeConfig['class']();
    }
}
