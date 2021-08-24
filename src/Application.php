<?php

declare(strict_types=1);

namespace KSamuel\RrService;

use KSamuel\RrService\Config\Config;
use KSamuel\RrService\Config\Storage;
use KSamuel\RrService\Connection\Manager;
use KSamuel\RrService\Service\DependencyContainer;
use KSamuel\RrService\Service\Loader\LoaderInterface;
use KSamuel\RrService\Service\Result;
use KSamuel\RrService\Service\ServiceInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

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
     * Performance statistics
     * @var array<string,mixed>
     */
    protected array $stat = [];
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
        // лог ошибок
        $this->logger = $this->diContainer->get(LoggerInterface::class);

        // доступные сервисы
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
     * @return array<array>
     * @throws \Exception
     */
    protected function getDebugData(): array
    {
        $debug = new \KSamuel\RrService\Debug($this->connectionManager, $this->config->get('debug_options'));
        return $debug->getInfo();
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
     * @param ResponseInterface $response
     * @return ResponseInterface $response
     * @throws \Exception
     */
    public function run(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $time = microtime(true);
        /**
         * @var RouterInterface $router
         */
        $router = $this->diContainer->get(RouterInterface::class);
        $result = $router->route($request, $this->diContainer->get(LoaderInterface::class), new Result());

        $error = $result->getError();
        if (!empty($error)) {
            $httpCode = $result->getHttpErrorCode();
            if ($httpCode !== null) {
                $code = $httpCode;
                $response = $response->withStatus($code);
                $respData = [
                    'success' => false,
                    'msg' => $response->getReasonPhrase(),
                    'status' => $response->getStatusCode(),
                    'detail' => $error
                ];
            } else {
                // записываем в ответ ошибки если они есть
                $respData = ['message' => $error];
            }
        } else {
            $respData = $result->getData();
        }

        if ($this->config->get('debug')) {
            $debugInfo = $this->getDebugData();
            if (isset($debugInfo['sql']['query'])) {
                foreach ($debugInfo['sql']['query'] as & $item) {
                    $item['q'] = (string)$item['q'];
                }
                unset($item);
            }
            $debugInfo['stat'] = $result->getDebugStat();
            $debugInfo['time'] = (float)number_format(microtime(true) - $time, 5);
            $debugInfo['mem_peak'] = number_format(memory_get_peak_usage() / 1024 / 1024, 3) . ' Mb';
            $debugInfo['mem_usage'] = number_format(memory_get_usage() / 1024 / 1024, 3) . ' Mb';
            $respData['debug'] = $debugInfo;
        }

        // disable browser caching
        $response->withAddedHeader('Cache-Control', 'no-store, no-cache, must-revalidate');
        $response->withAddedHeader('Pragma', 'no-cache');
        // create response
        $response->getBody()->write((string)json_encode($respData));

        // flush statistics
        $this->stat = [];
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
