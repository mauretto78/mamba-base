<?php

namespace Mamba\Command\Tests;

use gossi\codegen\generator\CodeGenerator;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpMethod;
use gossi\codegen\model\PhpParameter;
use Mamba\Command\ControllerCreateCommand;
use Mamba\Command\ControllerDeleteCommand;
use Mamba\Tests\MambaTest;
use Symfony\Component\Config\Definition\Exception\Exception;
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
