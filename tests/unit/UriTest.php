<?php

declare(strict_types=1);

namespace KSamuel\RrService;

use PHPUnit\Framework\TestCase;

class UriTest extends TestCase
{
    public function testGetPart()
    {
        $url = '/my/url/path';
        $uri = new Uri();
        $this->assertEquals('url', $uri->getPart($url, 1, null));
        $this->assertEquals('code', $uri->getPart($url, 3, 'code'));
        $this->assertEquals(null, $uri->getPart($url, 3));
    }
}
