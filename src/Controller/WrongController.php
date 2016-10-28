<?php

namespace Mamba\Controller;

use Mamba\Base\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * WrongNameController Class
 */
class WrongNameController extends BaseController {

	/**
	 * dummyAction
	 * 
	 * @param mixed $request
	 * @return Response
	 */
	public function dummyAction($request) {
		return new Response('dummy response');
	}
}