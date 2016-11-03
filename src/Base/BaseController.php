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

use Doctrine\ORM\EntityManager;
use Mamba\Base\BaseApplication as Container;

/**
 * Class BaseController.
 */
class BaseController
{
    /**
     * @var Container
     */
    protected $app;

    /**
     * @var \Twig_Environment
     */
    protected $view;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * Set the application class to the controller.
     *
     * @param Container $app
     */
    public function __construct(Container $app)
    {
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
     * @return \Twig_Environment
     */
    public function getView()
    {
        return $this->app->key('twig');
    }

    /**
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->app->key('orm.em ');
    }

    /**
     * @param $file
     * @param array $params
     *
     * @return mixed
     */
    public function render($file, $params = array())
    {
        return $this->getView()->render($file, $params);
    }
}
