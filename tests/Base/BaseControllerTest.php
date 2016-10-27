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

    public function testViewIsAnInstanceOfTwigEnvironment()
    {
        $this->assertInstanceOf('\Twig_Environment', $this->controller->getView());
    }

    public function testRenderView()
    {
        $view = $this->controller->render('base.html.twig');

        $html = '<!doctype html>' . PHP_EOL;
        $html .= '<html lang="en">'. PHP_EOL;
        $html .= '<head>'. PHP_EOL;
        $html .= '    <meta charset="UTF-8">'. PHP_EOL;
        $html .= '    <title>Mamba</title>'. PHP_EOL;
        $html .= '</head>'. PHP_EOL;
        $html .= '<body>'. PHP_EOL;
        $html .= '    Silence is golden.'. PHP_EOL;
        $html .= '</body>'. PHP_EOL;
        $html .= '</html>';

        $this->assertContains($view, $html);
    }
}
