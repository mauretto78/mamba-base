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
use Mamba\Contracts\BaseTypeInterface;
use Symfony\Component\Form\FormFactory;

/**
 * Class BaseType.
 */
abstract class BaseType implements BaseTypeInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Container
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
     * BaseType constructor.
     *
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
        $this->factory = $this->app->key('form.factory');
    }

    /**
     * @return Container
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @return FormFactory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
