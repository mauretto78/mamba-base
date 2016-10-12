<?php

/*
 * This file is part of the Mamba microframework.
 *
 * (c) Mauro Cassani <assistenza@easy-grafica.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mamba\Command;

use Mamba\Base\BaseCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class DebugDumpCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('app:debug:dump')
            ->setDescription('Dump all data in DI container.')
            ->setHelp('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}