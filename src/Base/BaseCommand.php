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

use Mamba\Base\BaseApplication as Container;
use Knp\Command\Command;

/**
 * Class BaseCommand.
 */
class BaseCommand extends Command
{
    /**
     * @var Container
     */
    protected $app;

    /**
     * BaseCommand constructor.
     *
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        parent::__construct('BaseCommand');

        $this->app = $app;
    }

    /**
     * @return Container
     */
    public function getApp()
    {
        return $this->app;
    }
}
