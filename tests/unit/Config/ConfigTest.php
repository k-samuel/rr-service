<?php

declare(strict_types=1);

namespace KSamuel\RrService\Config;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testGet(): void
    {
        $config = new Config('myConfig');
        $config->setValues(['first' => 1, 'second' => 2]);
        $this->assertEquals(1, $config->get('first'));
    }

    public function testGetInvalid(): void
    {
        $config = new Config('myConfig');
        $config->setValues(['first' => 1, 'second' => 2]);
        $this->expectException('InvalidArgumentException');
        $this->assertEquals(1, $config->get('undefined'));
    }

    public function testToArray(): void
    {
        $input = ['first' => 1, 'second' => 2];
        $config = new Config('myConfig');
        $config->setValues($input);
        $this->assertEquals($input, $config->__toArray());
    }
}
