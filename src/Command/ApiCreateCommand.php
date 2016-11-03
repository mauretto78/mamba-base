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
use Mamba\Services\ApiCreatorService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

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
        $helper = $this->getHelper('question');

        $entities = [];
        foreach (glob($this->app->getEntityDir().'/*') as $file) {
            $pathinfo = pathinfo($file);
            $entities[] = $pathinfo['filename'];
        }

        $question = new Question('<question>Please enter the name of the Entity:</question> ');
        $question->setAutocompleterValues($entities);
        $entity = $helper->ask($input, $output, $question);

        $question2 = new Question('<question>Please enter the version of your API:</question> ', '1.0');
        $version = $helper->ask($input, $output, $question2);

        $createApi = $this->_createApi($entity, $version);

        switch ($createApi) {
            case 0:
                $output->writeln('<error>Error creating API on Entity '.$entity.'.</error>');
                break;

            case 1:
                $output->writeln('<info>API on Entity '.$entity.' was successfully created.</info>');
                break;

            case 2:
                $output->writeln('<error>Entity '.$entity.' does not exists.</error>');
                break;
        }
    }

    /**
     * Create an API on an Entity.
     *
     * @param $entity
     * @param $version
     *
     * @return int
     */
    private function _createApi($entity, $version)
    {
        $file = $this->app->getEntityDir().'/'.$entity.'.php';

        // Check the file
        if (!file_exists($file)) {
            return 2;
        }

        // Create the API
        /* @var ApiCreatorService */
        $apiCreator = $this->getApp()->key('api_creator');
        $apiCreator->setEntity($entity);
        $apiCreator->setController($entity.'Controller');
        $apiCreator->setVersion($version);

        if ($apiCreator->create()) {
            return 1;
        }

        return 0;
    }
}
