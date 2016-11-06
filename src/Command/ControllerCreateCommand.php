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

use Memio\Model\Argument;
use Memio\Memio\Config\Build;
use Memio\Model\File;
use Memio\Model\FullyQualifiedName;
use Memio\Model\Method;
use Memio\Model\Object;
use Memio\Model\Phpdoc\ParameterTag;
use Memio\Model\Phpdoc\MethodPhpdoc;
use Mamba\Base\BaseCommand;
use Memio\Model\Phpdoc\ReturnTag;
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
                $output->writeln('<error>Error creating entity ' . $this->_getControllerName($controller) . '.</error>');
                break;

            case 1:
                $output->writeln('<info>Controller ' . $this->_getControllerName($controller) . ' was successfully created.</info>');
                break;

            case 2:
                $output->writeln('<error>File src/Controller/' . $this->_getControllerName($controller) . 'Controller.php already exists.</error>');
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
        return S::create($controller)->upperCamelize();
    }

    /**
     * @param $controller
     *
     * @return int
     */
    private function _createController($controller)
    {
        $controller = $this->_getControllerName($controller);
        $className = $this->app->getControllerNamespace() . $controller . 'Controller';
        $file = $this->app->getControllerDir() . '/' . $controller . 'Controller.php';

        // Duplicate file
        if (file_exists($file)) {
            return 2;
        }

        // Create Controller
        if ($newController = fopen($file, 'w')) {
            $code = $this->_generateController($file, $controller);
            fwrite($newController, $code);
            fclose($newController);

            return 1;
        }

        return 0;
    }

    /**
     * @param $file
     * @param $controller
     * @return string
     */
    private function _generateController($file, $controller)
    {
        $newController = File::make($file)
            ->addFullyQualifiedName(FullyQualifiedName::make('Mamba\Base\BaseController'))
            ->addFullyQualifiedName(FullyQualifiedName::make('Symfony\Component\HttpFoundation\Request'))
            ->addFullyQualifiedName(FullyQualifiedName::make('Symfony\Component\HttpFoundation\Response'))
            ->setStructure(
                Object::make('Mamba\Controller\\'.$controller.'Controller')
                    ->extend(Object::make('Mamba\Base\BaseController'))
                    ->addMethod(
                        Method::make('dummyAction')
                            ->setPhpdoc(MethodPhpdoc::make()
                                ->addParameterTag(new ParameterTag('Request', 'request'))
                                ->setReturnTag(new ReturnTag('Response'))
                            )
                            ->addArgument(new Argument('Request', 'request'))
                            ->setBody("\t\t".'return new Response(\'dummy response\');')
                    )
            )
        ;

        $prettyPrinter = Build::prettyPrinter();

        return $prettyPrinter->generateCode($newController);
    }
}
