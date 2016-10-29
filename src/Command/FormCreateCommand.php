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

use gossi\codegen\generator\CodeGenerator;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpMethod;
use Mamba\Base\BaseCommand;
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
        while(1 === 1){

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
     * @return S
     */
    private function _getFormName($form)
    {
        return  S::create($form)->upperCamelize();
    }

    /**
     * @param $form
     * @param array $fields
     * @return int
     */
    private function _createForm($form, $fields = [])
    {
        $form = $this->_getFormName($form);
        $class = '\Mamba\Type\\'.$form;
        $file = $this->app->getRootDir().'/src/Type/'.$form.'Type.php';

        // Duplicate file
        if (file_exists($file)) {
            return 2;
        }

        // Create Form
        if ($newForm = fopen($file, 'w')) {
            $txt = $this->_getFormCode($form, $fields);
            fwrite($newForm, $txt);
            fclose($newForm);

            return 1;
        }

        return 0;
    }

    /**
     * @param $form
     * @param $fields
     * @return string
     */
    private function _getFormCode($form, $fields)
    {
        $class = new PhpClass();
        $class
            ->setName($form.'Type extends BaseType')
            ->setNamespace('Mamba\\Type')
            ->setDescription($form.'Type Class')
            ->setMethod(PhpMethod::create('getForm')
                ->setDescription('getForm')
                ->setType('mixed')
                ->setBody($this->_getFieldsCode($form, $fields))
            )
            ->setMethod(PhpMethod::create('createView')
                ->setDescription('createView')
                ->setType('FormView')
                ->setBody('return $this->getForm()->createView();')
            )
            ->addUseStatement('Mamba\\Base\\BaseType')
            ->addUseStatement('Symfony\\Component\\Form\\Extension\\Core\\Type\\ChoiceType')
            ->addUseStatement('Symfony\\Component\\Form\\Extension\\Core\\Type\\EmailType')
            ->addUseStatement('Symfony\\Component\\Form\\Extension\\Core\\Type\\FormType')
            ->addUseStatement('Symfony\\Component\\Form\\Extension\\Core\\Type\\NumberType')
            ->addUseStatement('Symfony\\Component\\Form\\Extension\\Core\\Type\\TextType')
            ->addUseStatement('Symfony\\Component\\Form\\Extension\\Core\\Type\\TextareaType')
            ->addUseStatement('Symfony\\Component\\Form\\Form')
            ->addUseStatement('Symfony\\Component\\Form\\FormView')
            ->addUseStatement('Symfony\\Component\\Validator\\Constraints', 'Assert')
        ;
        $generator = new CodeGenerator();

        $code =  '<?php';
        $code .= "\n\n";
        $code .= $generator->generate($class);

        return $code;
    }

    /**
     * @param $form
     * @param $fields
     * @return string
     */
    private function _getFieldsCode($form, $fields)
    {
        $body = '$this->setName(\''.$form.'Type\');';
        $body .= PHP_EOL;
        $body .= PHP_EOL;
        $body .= '$form = $this->factory->createBuilder(FormType::class)';
        foreach ($fields as $key => $value){
            $body .= PHP_EOL;
            $body .= "\t".'->add(\''.$key.'\', '.$value.'Type::class, [';
            $body .= "\n\t\t".'\'label\' => \''.$key.'\',';
            $body .= "\n\t\t".'\'constraints\' => [';
            $body .= "\n\t\t\t".'new Assert\NotBlank(),';
            $body .= "\n\t\t".'],';
            $body .= "\n\t".'])';
        }
        $body .= PHP_EOL;
        $body .= ';';
        $body .= PHP_EOL;
        $body .= PHP_EOL;
        $body .= 'return $form->getForm();';

        return $body;
    }
}
