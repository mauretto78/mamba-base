<?php

namespace Mamba\Tests;

use Knp\Command\Command;
use Mamba\Base\BaseApplication as Application;
use Knp\Provider\ConsoleServiceProvider;
use Mamba\Providers\ConfigServiceProvider;

class MambaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Command
     */
    protected $command;

    /**
     * setUp method.
     */
    public function setUp()
    {
        $this->app = new Application('dev');
        $this->app->register(
            new ConsoleServiceProvider(),
            [
                'console.name' => 'Mamba Console Tester',
                'console.version' => '1.0.0',
                'console.project_directory' => __DIR__ . "/.."
            ]
        );
        $this->app->register(new ConfigServiceProvider(), [
            'config.CacheFilePath' => 'var/cache/cachefile',
            'config.baseDir' => __DIR__,
            'config.configFiles' => [
                '../config/dummy.yml',
                '../config/routing.yml',
            ],
        ]);
    }

    /**
     * @param Command $command
     */
    public function registerCommand(Command $command)
    {
        /** @var \Knp\Console\Application $console */
        $console = $this->app->key('console');

        $this->command = new $command($this->app);
        $this->app->addCommand($this->command);
        $console->add($this->command);
    }
}
