<?php

/*
 * This file is part of the Mamba microframework.
 *
 * (c) Mauro Cassani <assistenza@easy-grafica.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mamba\Base;

use Interop\Container\ContainerInterface;
use Mamba\Exception\ApplicationValueNotFoundException;
use Mamba\Exception\ApplicationException;
use Silex\Application;
use Silex\Route;
use Symfony\Component\HttpFoundation\Request;
use Mamba\Contracts\BaseApplicationInterface;

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
    protected $configFiles = [];

    /**
     * @var array
     */
    protected $serviceProviders = [];

    /**
     * @var array
     */
    protected $devServiceProviders = [];

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
     * @param array $configFiles
     */
    public function setConfigFiles($configFiles)
    {
        $this->configFiles = $configFiles;
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
    public function getConfigFiles()
    {
        return $this->configFiles;
    }

    /**
     * @return array
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * @return array
     */
    public function getServiceProviders()
    {
        return $this->serviceProviders;
    }

    /**
     * @return array
     */
    public function getDevServiceProviders()
    {
        return $this->devServiceProviders;
    }

    /**
     * @return array
     */
    public function getCommands()
    {
        return $this->commands;
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
     * Inits the ConfigServiceProvider.
     */
    public function initConfig()
    {
        $this->register(new \Mamba\Providers\ConfigServiceProvider(), [
            'config.CacheFilePath' => $this->getCacheFilePath(),
            'config.baseDir' => $this->getConfigDir(),
            'config.configFiles' => $this->getConfigFiles(),
        ]);
    }

    /**
     * Set the locale language of app.
     */
    public function initLocale()
    {
        $this->before(function () {
            $this['translator']->setLocale($this['config']['site']['language']);
        });
    }

    /**
     * Loads the routing system.
     */
    public function initRouting()
    {
        $app = $this;

        foreach ($this['config']['routings'] as $name => $routing) {
            $method = @$routing['method'] ?: 'get';

            // Register controller as service
            $route = explode('@', $routing['action']);
            $controller = strtolower(str_replace('\\', '.', $route[0]));
            $action = $route[1].'Action';

            $this[$controller] = function () use ($app, $route) {
                return new $route[0]($app);
            };

            /** @var Route $route */
            $route = $this->$method($routing['url'], $controller.':'.$action)->bind($name);

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
     * @param array $providers
     */
    public function initProviders($providers)
    {
        foreach ($providers as $provider => $values) {
            $this->serviceProviders[] = new $provider();
            $this->_registerProvider($provider, $values);
        }
    }

    /**
     * Register the dev providers.
     *
     * @param array $providers
     */
    public function initDevProviders($providers)
    {
        if ($this->getEnv() === 'dev') {
            foreach ($providers as $provider => $values) {
                $this->devServiceProviders[] = new $provider();
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

        $this->register($providerInstance, $values);
    }

    /**
     * @param $commands
     */
    public function initCommands($commands)
    {
        if ($this->getEnv() === 'dev') {
            foreach ($commands as $command) {
                $this->_registerCommand($command);
            }
        }
    }

    /**
     * @param $command
     * @param $params
     */
    protected function _registerCommand($command)
    {
        /** @var \Knp\Console\Application $console */
        $console = $this['console'];
        $commandInstance = new $command($this);

        if (!is_subclass_of($command, 'Knp\Command\Command')) {
            throw new \RuntimeException('Command class '.$command.' must extends Knp\Command\Command.');
        }

        $this->addCommand($commandInstance);
        $console->add($commandInstance);
    }

    /**
     * Custom error handlers.
     */
    public function initErrorHandler()
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
     *
     * @return bool
     */
    private function exceptionThrownByContainer(\InvalidArgumentException $exception)
    {
        $trace = $exception->getTrace()[0];

        return $trace['class'] === Application::class && $trace['function'] === 'offsetGet';
    }

    /**
     * Return the value from the container.
     * E.g.: $app['key'] === $app->pick('key').
     *
     * @param string $key
     *
     * @return mixed
     */
    public function key($key)
    {
        if (!$this->offsetExists($key)) {
            throw new ApplicationValueNotFoundException(sprintf('Identifier "%s" is not defined.', $key));
        }
        try {
            return $this->offsetGet($key);
        } catch (\InvalidArgumentException $exception) {
            if ($this->exceptionThrownByContainer($exception)) {
                throw new ApplicationException(
                    sprintf('Container error while retrieving "%s"', $key),
                    null,
                    $exception
                );
            } else {
                throw $exception;
            }
        }
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    public function set($key, $value)
    {
        if (!$this->has($key)) {
            $this[$key] = $value;
        }
    }

    /********************************************************************************
     * Magic methods for convenience
     *******************************************************************************/

    /**
     * @param $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->key($key);
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return $this->has($key);
    }

    /**
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    public function __set($key, $value)
    {
        return $this->set($key, $value);
    }
}
