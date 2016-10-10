<?php

namespace Mamba\Base\Tests;

use Mamba\Base\App\BaseApplication as Application;
use Mamba\Base\Providers\BaseControllerServiceProvider;

class BaseControllerServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    protected $app;

    public function setUp()
    {
        $this->app = new Application('dev');
        $this->app->register(new BaseControllerServiceProvider(), []);
    }

    public function testIfAppHasAnInstanceOfProvider()
    {
        $this->assertInstanceOf('Mamba\Base\Controller\BaseController', $this->app->key('base.controller'));
    }
}
