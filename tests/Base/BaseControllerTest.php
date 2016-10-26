<?php

namespace Mamba\Base\Tests;

use Mamba\Base\BaseController;
use Mamba\Tests\MambaTest;

class BaseControllerTest extends MambaTest
{
    /**
     * @var BaseController
     */
    protected $controller;

    public function setUp()
    {
        parent::setUp();
        $this->controller = new BaseController($this->app);
    }

    public function testHasAnApplicationInstance()
    {
        $this->assertInstanceOf('Mamba\Base\BaseApplication', $this->controller->getApp());
    }

    public function testRenderIsAnInstanceOfTwigEnvironment()
    {
        $this->assertInstanceOf('\Twig_Environment', $this->controller->render('test.html.twig'));
    }
}
