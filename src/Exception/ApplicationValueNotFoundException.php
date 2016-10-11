<?php

/*
 * This file is part of the Mamba microframework.
 *
 * (c) Mauro Cassani <assistenza@easy-grafica.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mamba\Exception;

use RuntimeException;
use Interop\Container\Exception\NotFoundException as InteropNotFoundException;

/**
 * ApplicationValueNotFoundException
 *
 * @author Mauro Cassani
 */
class ApplicationValueNotFoundException extends RuntimeException implements InteropNotFoundException
{
}