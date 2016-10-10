<?php

namespace Mamba\Base\Tests;

use Mamba\Base\App\BaseApplication as Application;
use Mamba\Base\Providers\ClientServiceProvider;

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
