<?php

/*
 * This file is part of the Mamba microframework.
 *
 * (c) Mauro Cassani <assistenza@easy-grafica.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mamba\Base\Type;

use App\Application;
use Symfony\Component\Form\FormFactory;

abstract class AbstractType
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var FormFactory
     */
    protected $factory;

    /**
     * @var array
     */
    protected $errors;

    /**
     * AbstractType constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->factory = $this->app['form.factory'];
    }
}
