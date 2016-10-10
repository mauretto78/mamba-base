<?php

namespace Mamba\Base\Tests;

use Mamba\Base\App\BaseApplication as Application;
use Mamba\Base\Providers\BaseCommandServiceProvider;

class BaseCommandServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    protected $app;

    public function setUp()
    {
        $this->app = new Application('dev');
        $this->app->register(new BaseCommandServiceProvider(), []);
    }

    public function testIfAppHasAnInstanceOfProvider()
    {
        $this->assertInstanceOf('Mamba\Base\Command\BaseCommand', $this->app->key('base.command'));
    }
}
