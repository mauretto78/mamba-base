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
use GuzzleHttp\Client;

/**
 * Class ClientServiceProvider.
 */
class ClientServiceProvider implements ServiceProviderInterface
{
    /**
     * Register this provider.
     *
     * @param Container $app
     */
    public function register(Container $app)
    {
        $app['client'] = function ($app) {
            $config = [];

            if (isset($app['guzzle.base_uri'])) {
                $config['base_uri'] = $app['guzzle.base_uri'];
            }
            if (isset($app['guzzle.timeout'])) {
                $config['timeout'] = $app['guzzle.timeout'];
            }
            if (isset($app['guzzle.debug'])) {
                $config['debug'] = $app['guzzle.debug'];
            }
            if (isset($app['guzzle.request_options']) && is_array($app['guzzle.request_options'])) {
                foreach ($app['guzzle.request_options'] as $valueName => $value) {
                    $config[$valueName] = $value;
                }
            }

            return new Client($config);
        };
    }
}
