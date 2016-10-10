<?php

namespace Mamba\Base\Tests;

use Mamba\Base\App\BaseApplication as Application;
use Mamba\Base\Command\BaseCommand;
use Mamba\Base\Providers\BaseCommandServiceProvider;
use Mamba\Base\Providers\ClientServiceProvider;
use Mamba\Base\Providers\ConfigServiceProvider;

class BaseApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    protected $app;

    public function setUp()
    {
        $this->app = new Application('dev');
    }

    public function testEnv()
    {
        $env = $this->app->getEnv();

        $this->assertEquals($env, 'dev');
    }

    public function testGetFromContainer()
    {
        $routingListener = $this->app->key('routing.listener');
        $this->assertInstanceOf(\Symfony\Component\HttpKernel\EventListener\RouterListener::class, $routingListener);
    }

    public function testSetAValueInContainer()
    {
        $this->app->set('foo', 'bar');
        $this->assertEquals('bar', $this->app->foo);
    }

    public function testSetACallbackInContainer()
    {
        $this->app->set('foo', function(){
            return new \GuzzleHttp\Psr7\Response();
        });
        $this->assertInstanceOf(\GuzzleHttp\Psr7\Response::class, $this->app->foo);
    }

    /**
     * @expectedException \Interop\Container\Exception\NotFoundException
     */
    public function testGetAValueFromContainerWithValueNotFoundError()
    {
        $this->app->key('foo');
    }

    /**
     * @expectedException \Interop\Container\Exception\NotFoundException
     */
    public function testGetAValueFromContainerWithDiConfigErrorThrownAsContainerValueNotFoundException()
    {
        $this->app->set('foo',  function() {
            return $this->app->key('doesnt-exist');
        });
        $this->app->key('foo');
    }
    
    public function testSettersAndGetters()
    {
        $this->app->setCacheDir('cachedir');
        $this->app->setConfigDir('configdir');
        $this->app->setLogsDir('logsdir');
        $this->app->setRootDir('rootdir');
        $this->app->setServerName('servername');
        $this->app->setViewDir('viewdir');

        $this->assertEquals($this->app->getCacheDir(), 'cachedir');
        $this->assertEquals($this->app->getConfigDir(), 'configdir');
        $this->assertEquals($this->app->getLogsDir(), 'logsdir');
        $this->assertEquals($this->app->getRootDir(), 'rootdir');
        $this->assertEquals($this->app->getServerName(), 'servername');
        $this->assertEquals($this->app->getViewDir(), 'viewdir');
    }
    
    public function testInitProviders()
    {
        $providersToRegister = [
            'require' =>
            [
                BaseCommandServiceProvider::class => [],
                ClientServiceProvider::class => [],
                ConfigServiceProvider::class => [],
            ]
        ];

        $this->app->initProviders($providersToRegister);

        $providers = $this->app->getProviders();
        $count = count($providers);

        $this->assertInstanceOf('Mamba\Base\Providers\BaseCommandServiceProvider', $providers[$count-3]);
        $this->assertInstanceOf('Mamba\Base\Providers\ClientServiceProvider', $providers[$count-2]);
        $this->assertInstanceOf('Mamba\Base\Providers\ConfigServiceProvider', $providers[$count-1]);
    }

    public function testInitCommands()
    {
        $commandsToRegister = [
            BaseCommand::class => [$this->app],
        ];

        $this->app->register(new \Knp\Provider\ConsoleServiceProvider(), [
            'console.name' => 'console demo',
            'console.version' => '1.2.3',
            'console.project_directory' => __DIR__,
        ]);

        $this->app->initCommands($commandsToRegister);

        $commands = $this->app->getCommands();
        $count = count($commands);

        $this->assertCount(1, $commands);
        $this->assertInstanceOf('Mamba\Base\Command\BaseCommand', $commands[$count-1]);
    }
}
