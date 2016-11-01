<?php

namespace Mamba\Base\Tests;

use Mamba\Tests\MambaTest;

class RoutingServiceProviderTest extends MambaTest
{
    public function testIfAppParsesTheCorrectedRoutes()
    {
        $this->app->initRouting();
        $routing = $this->app->routing;

        $this->assertTrue($this->app->has('routing'));
        $this->assertTrue($this->app->has('mamba.controller.dummycontroller'));
        $this->assertCount(6, $routing);

        foreach ($routing as $key=>$value){
            $this->assertInstanceOf('Silex\\Controller', $value);
        }
    }
}
