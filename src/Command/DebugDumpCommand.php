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

class DebugDumpCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('app:debug:dump')
            ->setDescription('Dump of all your application container.')
            ->setHelp('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $keys = $this->app->keys();
        asort($keys);

        $counter = 0;
        $rows = [];

        foreach ($keys as $key) {
            ++$counter;
            $getKey = $this->app->key($key);

            // check if $k is an object
            if (is_object($getKey)) {
                $type = '<error>class</error>';
                $value = get_class($getKey);
                // check if $k is an array
            } elseif (is_array($getKey)) {
                $type = '<comment>array</comment>';
                $value = '';
                $keys = array_keys($getKey);
                foreach ($keys as $k) {
                    $value .= '['.$k.'] ';
                }
                // check if $k is a string
            } else {
                $type = '<info>string</info>';
                $value = $getKey;
            }

            // populate rows
            $rows[] = [
                $key,
                $type,
                $value,
            ];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['Key', 'Type', 'Values or keys'])
            ->setRows($rows);

        $table->render();
    }
}
