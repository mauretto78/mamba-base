<?php

namespace Mamba\Base\Tests;

use Mamba\Base\App\BaseApplication as Application;
use Mamba\Base\Command\BaseCommand;

class BaseCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var BaseCommand
     */
    protected $command;

    public function setUp()
    {
        $this->app = new Application('dev');
        $this->command = new BaseCommand($this->app);
    }

    public function testHasAnApplicationInstance()
    {
        $this->assertInstanceOf('Mamba\Base\App\BaseApplication', $this->command->getApp());
    }

    public function testImplementsBaseCommandInterface()
    {
        $this->assertInstanceOf('Mamba\Base\Contracts\BaseCommandInterface', $this->command);
    }
}
