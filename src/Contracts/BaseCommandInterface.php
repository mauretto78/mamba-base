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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface BaseCommandInterface.
 *
 * @author Mauro Cassani
 */
interface BaseCommandInterface
{
    /**
     * @return mixed
     */
     public function configure();

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    public function execute(InputInterface $input, OutputInterface $output);
}