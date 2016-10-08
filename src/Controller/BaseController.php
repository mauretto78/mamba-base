<?php

/*
 * This file is part of the Mamba microframework.
 *
 * (c) Mauro Cassani <assistenza@easy-grafica.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mamba\Base\Controller;

use Mamba\Base\App\BaseApplication as Container;

class BaseController
{
    /**
     * @var Container
     */
    protected $app;

    /**
     * BaseController constructor.
     * 
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }
}
