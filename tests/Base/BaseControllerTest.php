<?php

namespace Mamba\Base\Tests;

use Mamba\Base\BaseApplication as Application;
use Mamba\Base\BaseController;

class BaseControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var BaseController
     */
    protected $controller;

    public function setUp()
    {
        $this->app = new Application('dev');
        $this->controller = new BaseController($this->app);
    }

    public function testHasAnApplicationInstance()
    {
        $this->assertInstanceOf('Mamba\Base\BaseApplication', $this->controller->getApp());
    }
}
