<?php

namespace Mamba\Providers\Tests;

use Mamba\Base\BaseApplication as Application;
use Mamba\Providers\ClientServiceProvider;
use Mamba\Tests\MambaTest;

class ClientServiceProviderTest extends MambaTest
{
    public function testIfAppHasAnInstanceOfProvider()
    {
        $this->assertInstanceOf('GuzzleHttp\Client', $this->app->client);
    }
}
