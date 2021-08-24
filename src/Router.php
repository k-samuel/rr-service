<?php

declare(strict_types=1);

namespace KSamuel\RrService;

use KSamuel\RrService\Service\Loader\LoaderInterface;
use KSamuel\RrService\Service\ResultInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Router
 * @package App
 */
class Router implements RouterInterface
{
    /**
     * Конфиг
     * @var array<string,mixed> $config
     */
    protected array $config;

    private Uri $uri;

    /**
     * Router constructor.
     * @param array <string,mixed>  $config
     * @param Uri $uri
     */
    public function __construct(array $config, Uri $uri)
    {
        $this->config = $config;
        $this->uri = $uri;
    }

    /**
     * Defining and launching a scenario based on the requested URL
     * @param ServerRequestInterface $request
     * @param LoaderInterface $loader
     * @param ResultInterface $result
     * @return ResultInterface
     * @throws \Exception
     */
    public function route(
        ServerRequestInterface $request,
        LoaderInterface $loader,
        ResultInterface $result
    ): ResultInterface {
        $defaultRoute = $this->config['default_service'] ?? null;
        $serviceCode = $this->uri->getPart($request->getUri()->getPath(), $this->config['uri_path_start_index']);

        $services = $this->config['services'];

        if (isset($services[$serviceCode])) {
            $route = $services[$serviceCode];
        } else {
            if ($defaultRoute === null || !isset($services[$defaultRoute])) {
                throw new \RuntimeException('Undefined default route ' . $defaultRoute);
            } else {
                $route = $services[$defaultRoute];
            }
        }

        $service = $loader->loadService($route);
        $service->run($request, $result);
        return $result;
    }
}
