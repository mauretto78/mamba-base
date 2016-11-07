<?php

namespace Mamba\Base\Tests;

use Mamba\Base\BaseCommand;
use Mamba\Tests\MambaTest;
use Mamba\Lib\Stringy as S;

class BaseCommandTest extends MambaTest
{
    /**
     * @var BaseCommand
     */
    protected $command;

    public function setUp()
    {
        parent::setUp();
        $this->command = new BaseCommand($this->app);
    }

    public function testHasAnApplicationInstance()
    {
        $this->assertInstanceOf('Mamba\Base\BaseApplication', $this->command->getApp());
    }

    public function testHasCorrectControllerNamespace()
    {
        $this->assertEquals('\Mamba\Controller\\', $this->app->getControllerNamespace());
    }

    public function testHasCorrectEntityNamespace()
    {
        $this->assertEquals('\Mamba\Entity\\', $this->app->getEntityNamespace());
    }
}
