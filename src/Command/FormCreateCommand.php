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
use Memio\Model\Argument;
use Memio\Memio\Config\Build;
use Memio\Model\File;
use Memio\Model\FullyQualifiedName;
use Memio\Model\Method;
use Memio\Model\Object;
use Memio\Model\Phpdoc\Description;
use Memio\Model\Phpdoc\MethodPhpdoc;
use Memio\Model\Phpdoc\ReturnTag;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\ChoiceQuestion;
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

        // Infinite loop
        while (1 === 1) {

            // Ask for fields and types
            $field = new Question('<question>Please enter field:</question> ', null);
            $type = new ChoiceQuestion(
                'Please select field type:',
                [
                    'Text',
                    'Email',
                    'Choice',
                    'Number',
                    'Textarea',
                ],
                0
            );
            $type->setErrorMessage('Type %s is invalid.');
            $fields[$helper->ask($input, $output, $field)] = $helper->ask($input, $output, $type);

            // confirmation question
            $question = new ConfirmationQuestion('Another fields?', false);

            if (!$confirm = $helper->ask($input, $output, $question)) {
                $createForm = $this->_createForm($form, $fields);

                switch ($createForm) {
                    case 0:
                        $output->writeln('<error>Error creating form '.$form.'Type.</error>');
                        break;

                    case 1:
                        $output->writeln('<info>Form '.$form.'Type was successfully created.</info>');
                        break;

                    case 2:
                        $output->writeln('<error>File src/Type/'.$form.'Type.php already exists.</error>');
                        break;
                }

                return;
            }
        }
    }

    /**
     * @param $form
     *
     * @return S
     */
    private function _getFormName($form)
    {
        return  S::create($form)->upperCamelize();
    }

    /**
     * @param $form
     * @param array $fields
     *
     * @return int
     */
    private function _createForm($form, $fields = [])
    {
        $form = $this->_getFormName($form);
        $class = '\Mamba\Type\\'.$form;
        $file = $this->app->getFormDir().'/'.$form.'Type.php';

        // Duplicate file
        if (file_exists($file)) {
            return 2;
        }

        // Create Form
        if ($newForm = fopen($file, 'w')) {
            $txt = $this->_getFormCode($file, $form, $fields);
            fwrite($newForm, $txt);
            fclose($newForm);

            return 1;
        }

        return 0;
    }

    /**
     * @param $file
     * @param $form
     * @param $fields
     *
     * @return string
     */
    private function _getFormCode($file, $form, $fields)
    {
        $newForm = File::make($file)
            ->addFullyQualifiedName(FullyQualifiedName::make('Mamba\Base\BaseType'))
            ->addFullyQualifiedName(FullyQualifiedName::make('Symfony\Component\Form\Extension\Core\Type\ChoiceType'))
            ->addFullyQualifiedName(FullyQualifiedName::make('Symfony\Component\Form\Extension\Core\Type\EmailType'))
            ->addFullyQualifiedName(FullyQualifiedName::make('Symfony\Component\Form\Extension\Core\Type\FormType'))
            ->addFullyQualifiedName(FullyQualifiedName::make('Symfony\Component\Form\Extension\Core\Type\NumberType'))
            ->addFullyQualifiedName(FullyQualifiedName::make('Symfony\Component\Form\Extension\Core\Type\TextType'))
            ->addFullyQualifiedName(FullyQualifiedName::make('Symfony\Component\Form\Extension\Core\Type\TextareaType'))
            ->addFullyQualifiedName(FullyQualifiedName::make('Symfony\Component\Form\Form'))
            ->addFullyQualifiedName(FullyQualifiedName::make('Symfony\Component\Form\FormView'))
            ->addFullyQualifiedName(FullyQualifiedName::make('Symfony\Component\Validator\Constraints as Assert'))
            ->setStructure(
                Object::make('Mamba\Type\\'.$form.'Type')
                    ->extend(Object::make('Mamba\Base\BaseType'))
                    ->addMethod(
                        Method::make('getForm')
                            ->setPhpdoc(MethodPhpdoc::make()
                                ->setDescription(Description::make('getForm'))
                                ->setReturnTag(new ReturnTag('mixed'))
                            )
                            ->setBody($this->_getFieldsCode($form, $fields))
                    )
                    ->addMethod(
                        Method::make('createView')
                            ->setPhpdoc(MethodPhpdoc::make()
                                ->setDescription(Description::make('createView'))
                                ->setReturnTag(new ReturnTag('FormView'))
                            )
                            ->setBody("\t\t".'return $this->getForm()->createView();')
                    )
            )
        ;

        $prettyPrinter = Build::prettyPrinter();

        return $prettyPrinter->generateCode($newForm);
    }

    /**
     * @param $form
     * @param $fields
     *
     * @return string
     */
    private function _getFieldsCode($form, $fields)
    {
        $body = "\t\t".'$this->setName(\''.$form.'Type\');';
        $body .= PHP_EOL;
        $body .= PHP_EOL;
        $body .= "\t\t".'$form = $this->factory->createBuilder(FormType::class)';
        foreach ($fields as $key => $value) {
            $body .= PHP_EOL;
            $body .= "\t\t\t".'->add(\''.$key.'\', '.$value.'Type::class, [';
            $body .= "\n\t\t\t\t".'\'label\' => \''.$key.'\',';
            $body .= "\n\t\t\t\t".'\'constraints\' => [';
            $body .= "\n\t\t\t\t\t".'new Assert\NotBlank(),';
            $body .= "\n\t\t\t\t".'],';
            $body .= "\n\t\t\t".'])';
        }
        $body .= PHP_EOL;
        $body .= "\t\t".';';
        $body .= PHP_EOL;
        $body .= PHP_EOL;
        $body .= "\t\t".'return $form->getForm();';

        return $body;
    }
}
