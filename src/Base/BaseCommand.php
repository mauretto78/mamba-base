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

    /**
     * @return string
     */
    public function getControllerNamespace()
    {
        return '\Mamba\Controller\\';
    }

    /**
     * @return string
     */
    public function getControllerDir()
    {
        return $this->app->getRootDir().'/src/Controller';
    }

    /**
     * @return string
     */
    public function getEntityNamespace()
    {
        return '\Mamba\Entity\\';
    }

    /**
     * @return string
     */
    public function getEntityDir()
    {
        return $this->app->getRootDir().'/src/Entity';
    }

    /**
     * @return string
     */
    public function getRepoDir()
    {
        return $this->app->getRootDir().'/src/Repository';
    }

    /**
     * @return string
     */
    public function getFormDir()
    {
        return $this->app->getRootDir().'/src/Type';
    }
}
