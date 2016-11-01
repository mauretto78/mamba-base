<?php

/*
 * This file is part of the Mamba microframework.
 *
 * (c) Mauro Cassani <assistenza@easy-grafica.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mamba\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Route;

/**
 * Class RoutingServiceProvider.
 */
class RoutingServiceProvider implements ServiceProviderInterface
{
    /**
     * Register this provider.
     *
     * @param Container $app
     *
     * @throws \Exception if config cache file is generated but is malformed or not readable
     */
    public function register(Container $app)
    {
        $app['routing'] = function ($app) {

            $routes = [];
            $registeredRoutings = [];

            if (isset($app['config.routes'])) {
                $routes = $app['config.routes'];
            }

            foreach ($routes as $name => $routing) {

                $methods = $this->_resolveMethod(@$routing['method']);

                // Register controller as service
                $route = explode('@', $routing['action']);
                $controller = strtolower(str_replace('\\', '.', $route[0]));
                $action = $route[1].'Action';

                $app[$controller] = function () use ($app, $route) {
                    return new $route[0]($app);
                };

                foreach ($methods as $method){
                    /** @var Route $route */
                    $route = $app->$method($routing['url'], $controller.':'.$action)->bind($name);

                    if (isset($routing['requirements'])) {
                        $route->setRequirements($routing['requirements']);
                    }

                    if (isset($routing['defaults'])) {
                        foreach ($routing['defaults'] as $parameter => $value) {
                            $route->value($parameter, $value);
                        }
                    }

                    $registeredRoutings[] = $route;
                }
            }

            return $registeredRoutings;
        };
    }

    /**
     * @param null $method
     * @return array
     */
    private function _resolveMethod($method = null)
    {

        if(!$method){
            return ['get'];
        }

        return explode('|', $method);
    }
}
