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

class ApiDeleteCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('app:api:delete')
            ->setDescription('Delete an API.')
            ->setHelp('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $entities = [];
        foreach (glob($this->app->getEntityDir().'/*') as $file) {
            $pathinfo = pathinfo($file);
            $entities[] = $pathinfo['filename'];
        }

        $question = new Question('<question>Please enter the name of the Entity:</question>');
        $question->setAutocompleterValues($entities);
        $entity = $helper->ask($input, $output, $question);

        $deleteApi = $this->_deleteApi($entity);

        switch ($deleteApi) {
            case 0:
                $output->writeln('<error>Error deleting API based on '.$entity.' Entity.</error>');
                break;

            case 1:
                $output->writeln('<info>API based on '.$entity.' Entity was successfully deleted.</info>');
                break;

            case 2:
                $output->writeln('<error>API based on '.$entity.' Entity does not exists.</error>');
                break;
        }
    }

    /**
     * Delete an API.
     *
     * @param $entity
     *
     * @return int
     */
    private function _deleteApi($entity)
    {
        $controller = $this->app->getControllerDir().'/'.$entity.'Controller.php';
        $entityFile = $this->app->getEntityDir().'/'.$entity.'.php';
        $repo = $this->app->getRepoDir().'/'.$entity.'Repository.php';

        // Check the files
        if (!file_exists($entityFile)) {
            return 2;
        }

        // Delete the Entity
        if (unlink($entityFile) and unlink($controller) and unlink($repo)) {
            return 1;
        }

        return 0;
    }
}
