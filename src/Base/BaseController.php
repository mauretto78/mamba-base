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
     * @param $file
     * @param array $params
     *
     * @return mixed
     */
    public function render($file, $params = array())
    {
        /** @var \Twig_Environment $twig */
        $twig = $this->app->key('twig');

        return $twig->render($file, $params);
    }
}
