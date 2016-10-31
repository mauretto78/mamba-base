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

class FormDeleteCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('app:form:delete')
            ->setDescription('Delete a Form.')
            ->setHelp('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $forms = [];
        foreach (glob($this->app->getFormDir().'/*') as $file) {
            $pathinfo = pathinfo($file);
            $forms[] = $pathinfo['filename'];
        }

        $question = new Question('<question>Please enter the name of the Form:</question> ');
        $question->setAutocompleterValues($forms);
        $form = $helper->ask($input, $output, $question);

        $deleteForm = $this->_deleteForm($form);

        switch ($deleteForm) {
            case 0:
                $output->writeln('<error>Error deleting entity '.$form.'.</error>');
                break;

            case 1:
                $output->writeln('<info>Form '.$form.' was successfully deleted.</info>');
                break;

            case 2:
                $output->writeln('<error>Form '.$form.' does not exists.</error>');
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
    private function _deleteForm($form)
    {
        $file = $this->app->getFormDir().'/'.$form.'.php';

        // Check the file
        if (!file_exists($file)) {
            return 2;
        }

        // Delete the Form
        if (unlink($file)) {
            return 1;
        }

        return 0;
    }
}
