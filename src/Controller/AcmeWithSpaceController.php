<?php

namespace Mamba\Controller;

use Symfony\Component\HttpFoundation\Response;
use Mamba\Base\BaseController;

class AcmeWithSpaceController extends BaseController
{
    public function dummyAction()
    {
        return new Response('dummy response');
    }
}