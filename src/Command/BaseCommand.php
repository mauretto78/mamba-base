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

use Mamba\Base\Contracts\CommandInterface;
use Pimple\Container;
use Knp\Command\Command;

class BaseCommand extends Command implements CommandInterface
{
    /**
     * @var Container
     */
    protected $app;

    /**
     * BaseCommand constructor.
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        parent::__construct();

        $this->app = $app;
    }
}
