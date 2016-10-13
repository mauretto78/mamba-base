<?php

namespace Mamba\Base\Tests;

use Mamba\Tests\MambaTest;

class ConfigServiceProviderTest extends MambaTest
{
    public function testIfAppHasCorrectedValues()
    {
        $config = $this->app->config;

        $this->assertEquals('Mamba Test', $config['app']['name']);
        $this->assertEquals('1.0.0', $config['app']['version']);
        $this->assertEquals('www.example.com', $config['app']['url']);
    }
}
