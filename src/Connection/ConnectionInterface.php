<?php

declare(strict_types=1);

namespace KSamuel\RrService\Connection;

use Laminas\Db\Adapter\Profiler\ProfilerInterface;

interface ConnectionInterface
{
    /**
     * @return void
     */
    public function close();

    /**
     * @return ProfilerInterface|null
     */
    public function getProfiler();
}
