<?php

/*
 * This file is part of the Mamba microframework.
 *
 * (c) Mauro Cassani <assistenza@easy-grafica.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mamba\Base\Contracts;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormView;

/**
 * Interface TypeInterface.
 *
 * @author Mauro Cassani
 */
interface TypeInterface
{
    /**
     * @return Form
     */
    public function getForm();

    /**
     * @return FormView
     */
    public function createView();
}
