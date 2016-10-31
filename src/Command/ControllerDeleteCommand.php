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
use Symfony\Component\Console\Question\Question;

class ControllerDeleteCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('app:controller:delete')
            ->setDescription('Delete a Controller.')
            ->setHelp('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $controllers = [];
        foreach (glob($this->app->getControllerDir().'/*') as $file) {
            $pathinfo = pathinfo($file);
            $controllers[] = $pathinfo['filename'];
        }

        $question = new Question('<question>Please enter the name of the Controller:</question> ');
        $question->setAutocompleterValues($controllers);
        $controller = $helper->ask($input, $output, $question);

        $deleteController = $this->_deleteController($controller);

        switch ($deleteController) {
            case 0:
                $output->writeln('<error>Error deleting controller '.$controller.'.</error>');
                break;

            case 1:
                $output->writeln('<info>Controller '.$controller.' was successfully deleted.</info>');
                break;

            case 2:
                $output->writeln('<error>Controller '.$controller.' does not exists.</error>');
                break;
        }
    }

    /**
     * Delete a Controller.
     *
     * @param $entity
     *
     * @return int
     */
    private function _deleteController($controller)
    {
        $file = $this->app->getControllerDir().'/'.$controller.'.php';

        // Check the file
        if (!file_exists($file)) {
            return 2;
        }

        // Delete the Entity
        if (unlink($file)) {
            return 1;
        }

        return 0;
    }
}
