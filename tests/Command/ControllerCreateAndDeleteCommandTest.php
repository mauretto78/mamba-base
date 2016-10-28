<?php

namespace Mamba\Command\Tests;

use gossi\codegen\generator\CodeGenerator;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpMethod;
use gossi\codegen\model\PhpParameter;
use Mamba\Command\ControllerCreateCommand;
use Mamba\Command\ControllerDeleteCommand;
use Mamba\Tests\MambaTest;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;

class ControllerCreateAndDeleteCommandTest extends MambaTest
{
    public function testDeletingNotWritableController()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testCreatingWrongNameControllerClass()
    {
        //create a fake Controller
        $file = $this->app->getRootDir().'/src/Controller/WrongController.php';

        if ($newController = fopen($file, 'w')) {
            $class = new PhpClass();
            $class
                ->setName('WrongNameController extends BaseController')
                ->setNamespace('Mamba\\Controller')
                ->setDescription('WrongNameController Class')
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
        }

        $this->setCommand(new ControllerCreateCommand($this->app));
        $commandTester = new CommandTester($this->command);

        /** @var QuestionHelper $helper */
        $helper = $this->command->getHelper('question');

        $helper->setInputStream($this->getInputStream('WrongName'));
        $commandTester->execute([

        ]);
        echo $output = $commandTester->getDisplay();
    }

    public function testDeletingNotExistingControllerFile()
    {
        $this->setCommand(new ControllerDeleteCommand($this->app));
        $commandTester = new CommandTester($this->command);

        /** @var QuestionHelper $helper */
        $helper = $this->command->getHelper('question');

        // 1. Create Controller
        $helper->setInputStream($this->getInputStream('NotExistingController'));
        $commandTester->execute([

        ]);
        $output = $commandTester->getDisplay();
        $this->assertContains('NotExistingController does not exists.', $output);
    }

    public function testExecute()
    {
        $this->setCommand(new ControllerCreateCommand($this->app));
        $commandTester = new CommandTester($this->command);

        /** @var QuestionHelper $helper */
        $helper = $this->command->getHelper('question');

        // 1. Create Controller
        $helper->setInputStream($this->getInputStream('Acme with space'));
        $commandTester->execute([

        ]);
        $output = $commandTester->getDisplay();
        $this->assertContains('Controller AcmeWithSpace was successfully created.', $output);

        // 2. Says Controller exists
        $helper->setInputStream($this->getInputStream('Acme with space'));
        $commandTester->execute([

        ]);
        $output = $commandTester->getDisplay();
        $this->assertContains('File src/Controller/AcmeWithSpaceController.php already exists.', $output);

        // 3. Delete Controller
        $this->setCommand(new ControllerDeleteCommand($this->app));
        $commandTester = new CommandTester($this->command);

        /** @var QuestionHelper $helper */
        $helper = $this->command->getHelper('question');
        $helper->setInputStream($this->getInputStream('AcmeWithSpaceController'));
        $commandTester->execute([

        ]);
        $output = $commandTester->getDisplay();
        $this->assertContains('Controller AcmeWithSpaceController was successfully deleted.', $output);
    }
}
