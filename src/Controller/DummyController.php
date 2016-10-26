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

use Symfony\Component\HttpFoundation\Response;
use Mamba\Base\BaseController;

class DummyController extends BaseController
{
    public function dummyAction()
    {
        return new Response('dummy response');
    }
}