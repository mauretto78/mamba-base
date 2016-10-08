<?php

/*
 * This file is part of the Mamba microframework.
 *
 * (c) Mauro Cassani <assistenza@easy-grafica.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mamba\Base\App;

use Interop\Container\ContainerInterface;
use Mamba\Base\Exception\ContainerException as MambaContainerException;
use Silex\Application;
use Silex\Route;
use Symfony\Component\HttpFoundation\Request;
use Mamba\Base\Contracts\BaseApplicationInterface;

class BaseApplication extends Application implements BaseApplicationInterface, ContainerInterface
{
    /**
     * @var string
     */
    protected $env;

    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @var string
     */
    protected $configDir;

    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * @var string
     */
    protected $logsDir;

    /**
     * @var string
     */
    protected $viewDir;

    /**
     * @var string
     */
    protected $serverName;

    /**
     * @var array
     */
    protected $providers = [];

    /**
     * @var array
     */
    protected $commands = [];

    /**
     * Kernel constructor.
     *
     * @param string $env
     */
    public function __construct($env = 'prod')
    {
        parent::__construct();

        $this->setEnv($env);
    }

    /**
     * @param string $env
     *
     * @return mixed
     */
    public function setEnv($env)
    {
        $this->env = $env;
    }

    /**
     * @param string $root
     *
     * @return mixed
     */
    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * @param string $configDir
     *
     * @return mixed
     */
    public function setConfigDir($configDir)
    {
        $this->configDir = $configDir;
    }

    /**
     * @param string $cacheDir
     *
     * @return mixed
     */
    public function setCacheDir($cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    /**
     * @param string $logsDir
     *
     * @return mixed
     */
    public function setLogsDir($logsDir)
    {
        $this->logsDir = $logsDir;
    }

    /**
     * @param string $viewDir
     *
     * @return mixed
     */
    public function setViewDir($viewDir)
    {
        $this->viewDir = $viewDir;
    }

    /**
     * @param string $serverName
     *
     * @return mixed
     */
    public function setServerName($serverName)
    {
        $this->serverName = $serverName;
    }

    /**
     * @param $provider
     * @param array $parameters
     *
     * @return mixed
     */
    public function addProvider($provider)
    {
        $this->providers[] = $provider;
    }

    /**
     * @param $command
     *
     * @return mixed
     */
    public function addCommand($command)
    {
        $this->commands[] = $command;
    }

    /**
     * @return string
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * @return string
     */
    public function getRootDir()
    {
        return $this->rootDir;
    }

    /**
     * @return string
     */
    public function getConfigDir()
    {
        return $this->configDir;
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        return $this->cacheDir;
    }

    private function getCacheFilePath()
    {
        return $this->getCacheDir().'/config.cache';
    }

    /**
     * @return string
     */
    public function getLogsDir()
    {
        return $this->logsDir;
    }

    /**
     * @return string
     */
    public function getViewDir()
    {
        return $this->viewDir;
    }

    /**
     * @return string
     */
    public function getServerName()
    {
        return $this->serverName;
    }

    /**
     * @return array
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * Sets the $app['env'].
     */
    protected function _setEnv()
    {
        $this['env'] = $this->env;
    }

    /**
     * Sets the $app['debug'].
     */
    protected function _setDebug()
    {
        $this['debug'] = $this->env === 'dev' || $this->env === 'test';
    }

    /**
     * Loads the config files.
     */
    protected function _initConfig()
    {
        $configFiles = [
            'app.yml',
            'config.yml',
            'parameters.yml',
            'routing.yml',
        ];

        $this->register(new \Mamba\Base\Providers\ConfigServiceProvider, [
            'config.CacheFilePath' => $this->getCacheFilePath(),
            'config.baseDir' => $this->getConfigDir(),
            'config.configFiles' => $configFiles,
        ]);
    }

    /**
     * Set the locale language of app.
     */
    protected function _initLocale()
    {
        $this->before(function () {
            $this['translator']->setLocale($this['config']['site']['language']);
        });
    }

    /**
     * Loads the routing system.
     */
    protected function _initRouting()
    {
        $app = $this;

        foreach ($this['config']['routings'] as $name => $routing) {
            $method = @$routing['method'] ?: 'get';

            /** @var Route $route */
            $route = $this->$method($routing['url'], $routing['action'].'Action')->bind($name);

            if (isset($routing['defaults'])) {
                foreach ($routing['defaults'] as $parameter => $value) {
                    $route->value($parameter, $value);
                }
            }
        }
    }

    /**
     * Register the providers.
     *
     * @param $providers
     */
    protected function _initProviders($providers)
    {
        foreach ($providers['require'] as $provider => $values) {
            $this->_registerProvider($provider, $values);
        }

        if ($this->getEnv() === 'dev') {
            foreach ($providers['require-dev'] as $provider => $values) {
                $this->_registerProvider($provider, $values);
            }
        }
    }

    /**
     * @param $provider
     * @param array $values
     */
    protected function _registerProvider($provider, array $values)
    {
        // check is $values is an array
        if (!is_array($values)) {
            throw new \RuntimeException('Values provided for the Provider '.$provider.' must be an array.');
        }

        $providerInstance = new $provider();

        // check if provider instance implements ServiceProviderInterface interface
        if (!$providerInstance instanceof \Pimple\ServiceProviderInterface) {
            throw new \RuntimeException('Provider '.$provider.' must be an instance of \Pimple\ServiceProviderInterface interface.');
        }

        $this->addProvider($provider);
        $this->register($providerInstance, $values);
    }

    /**
     * Register the commands.
     *
     * @param $commands
     */
    protected function _initCommands($commands)
    {
        if ($this->getEnv() === 'dev') {
            foreach ($commands as $command => $params) {
                $this->_registerCommand($command, $params);
            }
        }

    }

    /**
     * @param $command
     * @param $params
     */
    protected function _registerCommand($command, $params)
    {
        /** @var \Knp\Console\Application $console */
        $console = $this['console'];

        // check is $values is an array
        if (!is_array($params)) {
            throw new \RuntimeException('Params provided for the Command '.$command.' must be an array.');
        }

        $refl = new \ReflectionClass($command);
        $commandClass = $refl->newInstanceArgs($params);

        if (!is_subclass_of($commandClass, 'Knp\Command\Command')) {
            throw new \RuntimeException('Command class '.$command.' must extends Knp\Command\Command.');
        }

        $this->addCommand($command);
        $console->add($commandClass);
    }

    /**
     * Custom error handlers.
     */
    protected function _initErrorHandler()
    {
        $this->error(function (\Exception $e, Request $request, $code) {

            // exit if in debug mode
            if ($this['debug']) {
                return;
            }

            // custom error page
            switch ($code) {
                case 404:
                    return $this['twig']->render('errors/404.html.twig', []);
                    break;
                case 500:
                    return $this['twig']->render('errors/500.html.twig', []);
                    break;
                default:
                    return $this['twig']->render('errors/generic.html.twig', []);
            }
        });
    }

    /********************************************************************************
     * Methods to satisfy Interop\Container\ContainerInterface
     *
     * modified from: https://github.com/slimphp/Slim/blob/3.x/Slim/Container.php
     *******************************************************************************/

    /**
     * @param \InvalidArgumentException $exception
     * @return bool
     */
    private function exceptionThrownByContainer(\InvalidArgumentException $exception)
    {
        $trace = $exception->getTrace()[0];

        return $trace['class'] === Application::class && $trace['function'] === 'offsetGet';
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function get($id)
    {
        if (!$this->offsetExists($id)) {
            throw new \RuntimeException(sprintf('Identifier "%s" is not defined.', $id));
        }
        try {
            return $this->offsetGet($id);
        } catch (\InvalidArgumentException $exception) {
            if ($this->exceptionThrownByContainer($exception)) {
                throw new MambaContainerException(
                    sprintf('Container error while retrieving "%s"', $id),
                    null,
                    $exception
                );
            } else {
                throw $exception;
            }
        }
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has($id)
    {
        return $this->offsetExists($id);
    }

    /********************************************************************************
     * Magic methods for convenience
     *******************************************************************************/

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->has($name);
    }
}
