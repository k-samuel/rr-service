<?php

declare(strict_types=1);

namespace KSamuel\RrService;

use KSamuel\RrService\Config\Config;
use KSamuel\RrService\Config\Storage;
use KSamuel\RrService\Connection\Manager;
use KSamuel\RrService\Router\RouterInterface;
use KSamuel\RrService\Service\DependencyContainer;
use KSamuel\RrService\Service\Loader\LoaderInterface;
use KSamuel\RrService\Service\ServiceInterface;
use Nyholm\Psr7\Stream;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use JsonException;

/**
 * Application server
 * @package App
 * @author Kirill Yegorov 2021
 */
class Application
{
    public const MODE_WEB = 0;
    public const MODE_CLI = 1;
    /**
     * @var Config $config
     */
    protected Config $config;

    /**
     * @var LoggerInterface $logger
     */
    protected LoggerInterface $logger;

    /**
     * @var Manager
     */
    protected Manager $connectionManager;
    /**
     * Initialized services
     * @var ServiceInterface[]<string,ServiceInterface>
     */
    protected array $services = [];

    protected Storage $configStorage;

    private ContainerInterface $diContainer;

    /**
     * Server constructor.
     * @param array<string,mixed> $commonConfig
     * @throws \Exception
     */
    public function __construct(array $commonConfig)
    {
        // config files storage
        $this->configStorage = new Storage($commonConfig['config_common'], $commonConfig['config_path']);

        $this->diContainer = new DependencyContainer();
        $this->diContainer->bind(Storage::class, $this->configStorage);
        $this->diContainer->bindArray($this->configStorage->get('dependency.php')->__toArray());

        // application config
        $this->config = $this->diContainer->get('server.config');
        // connections manager
        $this->connectionManager = $this->diContainer->get(Manager::class);
        // error logger
        $this->logger = $this->diContainer->get(LoggerInterface::class);

        // available services
        if ($this->diContainer->get('application.mode') === self::MODE_WEB) {
            $this->services = $this->loadServices();
        }
    }

    /**
     * Load services in memory
     * @return ServiceInterface[]<string,ServiceInterface>
     * @throws \Exception
     */
    private function loadServices(): array
    {
        /**
         * @var LoaderInterface $loader
         */
        $loader = $this->diContainer->get(LoaderInterface::class);
        $routes = $this->diContainer->get('router.config')['services'];
        $services = [];
        foreach ($routes as $settings) {
            if (isset($services[$settings['class']])) {
                continue;
            }
            $services[$settings['class']] = $loader->loadService($settings);
        }
        return $services;
    }

    /**
     * Warmup services
     * @return void
     */
    public function warmUp(): void
    {
        foreach ($this->services as $service) {
            $service->warmup();
        }
    }

    /**
     * Reset external connections
     * @return void
     */
    public function resetConnections(): void
    {
        $this->connectionManager->resetConnections();
    }

    /**
     * Reset debugger statistics
     */
    public function resetDebug(): void
    {
        $this->connectionManager->resetProfiles();
    }

    /**
     * @param float $appStart
     * @return array<array>
     * @throws \Exception
     */
    protected function getDebugData(float $appStart): array
    {
        $debug = new \KSamuel\RrService\Debug($this->connectionManager, $this->config->get('debug_options'));
        $info = $debug->getInfo();
        if (isset($info['sql']['query'])) {
            foreach ($info['sql']['query'] as & $item) {
                $item['q'] = (string)$item['q'];
            }
            unset($item);
        }
        $info['time'] = (float)number_format(microtime(true) - $appStart, 5);
        $info['mem_peak'] = number_format(memory_get_peak_usage() / 1024 / 1024, 3) . ' Mb';
        $info['mem_usage'] = number_format(memory_get_usage() / 1024 / 1024, 3) . ' Mb';
        return $info;
    }

    /**
     * Clear runtime cache
     * @return bool
     */
    public function resetRuntimeCache(): bool
    {
        return $this->connectionManager->getRuntimeCache()->clean();
    }

    /**
     * Handle Request
     * @param ServerRequestInterface $request
     * @return ResponseInterface $response
     * @throws \Exception
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $time = microtime(true);
        /**
         * @var RouterInterface $router
         */
        $router = $this->diContainer->get(RouterInterface::class);
        $response = $router->route($request, $this->diContainer->get(LoaderInterface::class));

        // Add debug info for success json response
        if ($this->config->get('debug') && $response->getStatusCode() === 200) {
            try {
                $data = json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                $data = null;
            }
            if (is_array($data)) {
                $data['debug'] = $this->getDebugData($time);
                $response = $response->withBody(Stream::create((string)json_encode($data)));
            }
        }

        // disable browser caching
        $response->withAddedHeader('Cache-Control', 'no-store, no-cache, must-revalidate');
        $response->withAddedHeader('Pragma', 'no-cache');

        // flush statistics
        if ($this->config->get('debug')) {
            $this->resetDebug();
            $opts = $this->config->get('debug_options');
            if ($opts['sql']) {
                $this->connectionManager->resetConnections();
            }
        }
        return $response;
    }
}
