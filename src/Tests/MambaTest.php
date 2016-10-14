<?php

namespace Mamba\Tests;

use Doctrine\ORM\EntityManager;
use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Mamba\Base\BaseApplication as Application;
use Mamba\Providers\ConfigServiceProvider;
use Mamba\Providers\ClientServiceProvider;
use Knp\Command\Command;
use Knp\Provider\ConsoleServiceProvider;
use Silex\Provider\DoctrineServiceProvider;

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
     * @var EntityManager
     */
    protected $em;

    /**
     * setUp the Application and register providers.
     */
    public function setUp()
    {
        $this->app = new Application('dev');
        $this->app->setRootDir(__DIR__.'/../..');
        $this->app->setCacheDir($this->app->getRootDir().'/var/cache');

        $this->app->register(
            new ConsoleServiceProvider(),
            [
                'console.name' => 'Mamba Console Tester',
                'console.version' => '1.0.0',
                'console.project_directory' => __DIR__.'/..',
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
        $this->app->register(new DoctrineServiceProvider(), [
            'db.options' => [
                'driver' => 'pdo_mysql',
                'host' => 'localhost',
                'dbname' => 'mamba_test',
                'user' => 'root',
                'password' => '',
                'charset' => 'utf8mb4',
            ],
        ]);
        $this->app->register(new DoctrineOrmServiceProvider(), [
            'orm.proxies_dir' => $this->app->getCacheDir().'/doctrine/proxies',
            'orm.em.options' => [
                'mappings' => [
                    [
                        'use_simple_annotation_reader' => false,
                        'type' => 'annotation',
                        'namespace' => 'Mamba\Entity',
                        'path' => __DIR__.'/../src/Entity',
                    ],
                ],
            ],
        ]);
        $this->app->register(new ClientServiceProvider(), []);
    }

    /**
     * @param Command $command
     */
    public function setCommand(Command $command)
    {
        /** @var \Knp\Console\Application $console */
        $console = $this->app->key('console');

        $this->command = new $command($this->app);
        $this->app->addCommand($this->command);
        $console->add($this->command);
    }

    /**
     * @param EntityManager $em
     */
    public function setEm($em)
    {
        $this->em = $em;
    }
    
    /**
     * @param $input
     *
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
