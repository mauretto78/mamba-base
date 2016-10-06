<?php

/*
 * This file is part of the Mamba microframework.
 *
 * (c) Mauro Cassani <assistenza@easy-grafica.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mamba\Base\Command;

use Silex\Application;
use Knp\Command\Command;

abstract class BaseCommand extends Command
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * BaseCommand constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct('');

        $this->app = $app;
    }
}
