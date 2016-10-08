<?php

/*
 * This file is part of the Mamba microframework.
 *
 * (c) Mauro Cassani <assistenza@easy-grafica.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mamba\Base\Exception;
use InvalidArgumentException;
use Interop\Container\Exception\ContainerException as InteropContainerException;

/**
 * Container Exception
 *
 * @author Mauro Cassani
 */
class ContainerException extends InvalidArgumentException implements InteropContainerException
{
}