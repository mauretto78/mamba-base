<?php

namespace Mamba\Base\Tests;

use Mamba\Base\App\BaseApplication as Application;
use Mamba\Base\Providers\ConfigServiceProvider;

class ConfigServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    protected $app;

    public function setUp()
    {
        $this->app = new Application('dev');
        $this->app->register(new ConfigServiceProvider(), [
            'config.CacheFilePath' => 'var/cache/cachefile',
            'config.baseDir' => __DIR__,
            'config.configFiles' => [
                '../config/dummy.yml',
                '../config/routing.yml',
            ],
        ]);
    }

    public function testIfAppHasCorrectedValues()
    {
        $config = $this->app->config;

        $this->assertEquals('Mamba Test', $config['app']['name']);
        $this->assertEquals('1.0.0', $config['app']['version']);
        $this->assertEquals('www.example.com', $config['app']['url']);
    }
}
