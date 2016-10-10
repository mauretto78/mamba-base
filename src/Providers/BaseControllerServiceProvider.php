<?php

/*
 * This file is part of the Mamba microframework.
 *
 * (c) Mauro Cassani <assistenza@easy-grafica.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mamba\Base\Providers;

use Mamba\Base\Controller\BaseController;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class BaseControllerServiceProvider
 * @package Mamba\Base\Providers
 */
class BaseControllerServiceProvider implements ServiceProviderInterface
{
    /**
     * Register this provider.
     *
     * @param Container $app
     */
    public function register(Container $app)
    {
        $app['base.controller'] = function($app) {
            return new BaseController($app);
        };
    }
}
