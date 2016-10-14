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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ApiCreateCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('app:api:create')
            ->setDescription('Create a restful API.')
            ->setHelp('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}