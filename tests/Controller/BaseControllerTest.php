<?php

namespace Mamba\Base\Tests;

use Mamba\Base\App\BaseApplication as Application;
use Mamba\Base\Controller\BaseController;

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
        $this->assertInstanceOf('Mamba\Base\App\BaseApplication', $this->controller->getApp());
    }
}
