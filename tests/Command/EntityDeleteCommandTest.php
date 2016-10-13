<?php

namespace Mamba\Command\Tests;

use Mamba\Base\BaseApplication as Application;

class EntityDeleteCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    protected $app;

    public function setUp()
    {
        $this->app = new Application('dev');
    }

    public function testIfAppHasAnInstanceOfProvider()
    {
        
    }
}
