<?php

namespace Mamba\Tests;

use Knp\Command\Command;
use Mamba\Base\BaseApplication as Application;
use Knp\Provider\ConsoleServiceProvider;
use Mamba\Providers\ConfigServiceProvider;
use Mamba\Providers\ClientServiceProvider;

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
     * setUp the Application and register providers.
     */
    public function setUp()
    {
        $this->app = new Application('dev');
        $this->app->setRootDir(__DIR__.'/../..');
        $this->app->register(
            new ConsoleServiceProvider(),
            [
                'console.name' => 'Mamba Console Tester',
                'console.version' => '1.0.0',
                'console.project_directory' => __DIR__ . "/.."
            ]
        );
        $this->app->register(new ConfigServiceProvider(), [
            'config.CacheFilePath' => __DIR__.'/../../var/cache/cachefile',
            'config.baseDir' => __DIR__.'/../../tests',
            'config.configFiles' => [
                'config/dummy.yml',
                'config/routing.yml',
            ],
        ]);
        $this->app->register(new ClientServiceProvider(), []);
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

    /**
     * @param $input
     * @return resource
     */
    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $input);
        rewind($stream);

        return $stream;
    }
}
