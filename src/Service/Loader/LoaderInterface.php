<?php

/**
 * @author Kirill Yegorov 2021
 */

declare(strict_types=1);

namespace KSamuel\RrService\Service\Loader;

use KSamuel\RrService\Service\ServiceInterface;

/**
 * Service loader interface
 */
interface LoaderInterface
{
    /**
     * @param array{class:string,dependency:string} $serviceConfig
     * @return ServiceInterface
     */
    public function loadService(array $serviceConfig): ServiceInterface;
}
