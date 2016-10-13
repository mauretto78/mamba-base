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

class EntityDeleteCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('app:entity:delete')
            ->setDescription('Delete an Entity.')
            ->setHelp('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $entities = [];
        foreach (glob($this->getEntityDir().'/*') as $file) {
            $pathinfo = pathinfo($file);
            $entities[] = $pathinfo['filename'];
        }

        $question = new Question('<question>Please enter the name of the Entity:</question> ');
        $question->setAutocompleterValues($entities);
        $entity = $helper->ask($input, $output, $question);

        $deleteEntity = $this->_deleteEntity($entity);

        switch ($deleteEntity) {
            case 0:
                $output->writeln('<error>Error deleting entity '.$entity.'.</error>');
                break;

            case 1:
                $output->writeln('<info>Entity '.$entity.' was successfully deleted.</info>');
                break;

            case 2:
                $output->writeln('<error>Entity '.$entity.' does not exists.</error>');
                break;
        }
    }

    /**
     * Delete an Entity.
     *
     * @param $entity
     *
     * @return int
     */
    private function _deleteEntity($entity)
    {
        $file = $this->getEntityDir().'/'.$entity.'.php';
        $repo = $this->getRepoDir().'/'.$entity.'Repository.php';

        // Check the file
        if (!file_exists($file) and !file_exists($repo)) {
            return 2;
        }

        // Delete the Entity
        if (unlink($file) and unlink($repo)) {
            return 1;
        }

        return 0;
    }
}
