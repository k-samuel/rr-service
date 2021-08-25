<?php

declare(strict_types=1);

namespace KSamuel\RrService\Config;

use PHPUnit\Framework\TestCase;

class StorageTest extends TestCase
{
    private function getStorage(): Storage
    {
        return new Storage('tests/data/common/', 'tests/data/local/');
    }

    public function testGetConfigPath(): void
    {
        $this->assertEquals('tests/data/local/', $this->getStorage()->getConfigDir());
    }

    public function testGetCommon(): void
    {
        $storage = $this->getStorage();
        $config = $storage->get('tests/data/common/test_config.php', true);
        $this->assertEquals(2, $config->get('b'));
    }

    public function testGetMerged(): void
    {
        $storage = $this->getStorage();
        $config = $storage->get('test_config.php');
        $this->assertEquals(2, $config->get('b'));
        $this->assertEquals(3, $config->get('c'));
    }

    public function testUndefinedConfig(): void
    {
        $storage = $this->getStorage();
        $this->expectException('InvalidArgumentException');
        $config = $storage->get('undefined.php');
    }
}
