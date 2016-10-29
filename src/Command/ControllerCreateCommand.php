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
use gossi\codegen\model\PhpParameter;
use Mamba\Base\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Stringy\Stringy as S;

class ControllerCreateCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('app:controller:create')
            ->setDescription('Create a Controller.')
            ->setHelp('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $question = new Question('<question>Please enter the name of the Controller:</question> ', 'Acme');
        $question->setValidator(function ($value) {
            if (trim($value) == '') {
                throw new \Exception('The Controller name can not be empty');
            }

            return $value;
        });
        $controller = $helper->ask($input, $output, $question);

        $createController = $this->_createController($controller);

        switch ($createController) {
            case 0:
                $output->writeln('<error>Error creating entity '.$this->_getControllerName($controller).'.</error>');
                break;

            case 1:
                $output->writeln('<info>Controller '.$this->_getControllerName($controller).' was successfully created.</info>');
                break;

            case 2:
                $output->writeln('<error>File src/Controller/'.$this->_getControllerName($controller).'Controller.php already exists.</error>');
                break;
        }
    }

    /**
     * @param $controller
     *
     * @return S
     */
    private function _getControllerName($controller)
    {
        return  S::create($controller)->upperCamelize();
    }

    /**
     * @param $controller
     *
     * @return int
     */
    private function _createController($controller)
    {
        $controller = $this->_getControllerName($controller);
        $className = $this->getControllerNamespace().$controller.'Controller';
        $file = $this->getControllerDir().'/'.$controller.'Controller.php';

        // Duplicate file
        if (file_exists($file)) {
            return 2;
        }

        // Create Controller
        if ($newController = fopen($file, 'w')) {
            $class = new PhpClass();
            $class
                ->setName($controller.'Controller extends BaseController')
                ->setNamespace('Mamba\\Controller')
                ->setDescription($controller.'Controller Class')
                ->setMethod(PhpMethod::create('dummyAction')
                    ->addParameter(PhpParameter::create('request'))
                    ->setDescription('dummyAction')
                    ->setType('Response')
                    ->setBody('return new Response(\'dummy response\');')
                )
                ->addUseStatement('Mamba\\Base\\BaseController')
                ->addUseStatement('Symfony\\Component\\HttpFoundation\\Response')
                ->addUseStatement('Symfony\\Component\\HttpFoundation\\Request')
            ;
            $generator = new CodeGenerator();

            $code = '<?php';
            $code .= "\n\n";
            $code .= $generator->generate($class);

            fwrite($newController, $code);
            fclose($newController);

            return 1;
        }

        return 0;
    }
}
