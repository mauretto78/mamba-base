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

use Mamba\Base\Command\BaseCommand;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class BaseCommandServiceProvider
 * @package Mamba\Base\Providers
 */
class BaseCommandServiceProvider implements ServiceProviderInterface
{
    /**
     * Register this provider.
     *
     * @param Container $app
     */
    public function register(Container $app)
    {
        $app['base.command'] = function($app) {
            return new BaseCommand($app);
        };
    }
}
