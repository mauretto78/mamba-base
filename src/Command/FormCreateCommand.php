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
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Stringy\Stringy as S;

class FormCreateCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('app:form:create')
            ->setDescription('Create a Form.')
            ->setHelp('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $fields = [];

        $question = new Question('<question>Please enter the name of the Form:</question> ', 'Acme');
        $question->setValidator(function ($value) {
            if (trim($value) == '') {
                throw new \Exception('The Form name can not be empty');
            }

            return $value;
        });
        $form = $helper->ask($input, $output, $question);

        // Ask for fields
        $field = new Question('<question>Please enter field:</question> ', null);
        $fields[] = $helper->ask($input, $output, $field);

        // Infinite loop
        while(1 === 1){
            $question = new ConfirmationQuestion('Another fields?', false);

            if (!$confirm = $helper->ask($input, $output, $question)) {
                $this->_createForm($form, $fields);

                return;
            } else {
                $field = new Question('<question>Please enter field:</question> ', null);

                $fields[] = $helper->ask($input, $output, $field);
            }
        }
    }

    /**
     * @param $form
     * @return S
     */
    private function _getFormName($form)
    {
        return  S::create($form)->upperCamelize();
    }

    private function _createForm($form, $fields = null)
    {
        var_dump($fields);
    }
}
