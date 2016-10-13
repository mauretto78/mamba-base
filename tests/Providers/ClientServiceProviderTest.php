<?php

namespace Mamba\Providers\Tests;

use Mamba\Base\BaseApplication as Application;
use Mamba\Providers\ClientServiceProvider;

class ClientServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    protected $app;

    public function setUp()
    {
        $this->app = new Application('dev');
        $this->app->register(new ClientServiceProvider(), []);
    }

    public function testIfAppHasAnInstanceOfProvider()
    {
        $this->assertInstanceOf('GuzzleHttp\Client', $this->app->client);
    }
}
