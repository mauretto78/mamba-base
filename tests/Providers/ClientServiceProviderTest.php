<?php

namespace Mamba\Providers\Tests;

use Mamba\Tests\MambaTest;

class ClientServiceProviderTest extends MambaTest
{
    public function testIfAppHasAnInstanceOfProvider()
    {
        $this->assertInstanceOf('GuzzleHttp\Client', $this->app->client);
    }
}
