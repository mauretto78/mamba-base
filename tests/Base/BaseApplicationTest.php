<?php

namespace Mamba\Base\Tests;

use Mamba\Base\BaseCommand;
use Mamba\Providers\ClientServiceProvider;
use Mamba\Providers\ConfigServiceProvider;
use Mamba\Tests\MambaTest;

class BaseApplicationTest extends MambaTest
{
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
        $this->app->set('foo', function () {
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
        $this->app->set('foo',  function () {
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

    public function testInitRouting()
    {
        $this->app->initRouting();

        $this->assertTrue($this->app->has('mamba.controller.dummycontroller'));
        $this->assertInstanceOf('Silex\Controller', $this->app->get('/dummy-url'));
    }

    public function testInitProviders()
    {
        $this->app->setServiceProviders(
            [
                ConfigServiceProvider::class => [],
            ]
        );

        $this->app->setDevServiceProviders(
            [
                ClientServiceProvider::class => [],
            ]
        );

        $this->app->initProviders();
        $providers = $this->app->getServiceProviders();
        $devProviders = $this->app->getDevServiceProviders();

        $this->assertCount(1, $providers);
        $this->assertCount(1, $devProviders);
        
        foreach ($providers[0] as $provider => $values){
            $this->assertEquals('Mamba\Providers\ConfigServiceProvider', $provider);
        }

        foreach ($devProviders[0] as $provider => $values){
            $this->assertEquals('Mamba\Providers\ClientServiceProvider', $provider);
        }
    }

    public function testInitCommands()
    {
        $this->app->setCommands(
            [
                BaseCommand::class,
            ]
        );

        $this->app->register(new \Knp\Provider\ConsoleServiceProvider(), [
            'console.name' => 'console demo',
            'console.version' => '1.2.3',
            'console.project_directory' => __DIR__,
        ]);

        $this->app->initCommands();

        $commands = $this->app->getCommands();
        $count = count($commands);

        $this->assertCount(1, $commands);
        $this->assertEquals('Mamba\Base\BaseCommand', $commands[$count - 1]);
    }
}
