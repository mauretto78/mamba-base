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
use Mamba\Services\ApiCreatorService;

/**
 * Class ApiCreatorServiceProvider.
 */
class ApiCreatorServiceProvider implements ServiceProviderInterface
{
    /**
     * Register this provider.
     *
     * @param Container $app
     */
    public function register(Container $app)
    {
        $app['api_creator'] = function ($app) {

            return new ApiCreatorService($app);
        };
    }
}
