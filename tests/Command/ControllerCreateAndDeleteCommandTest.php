<?php

namespace Mamba\Command\Tests;

use Mamba\Command\ControllerCreateCommand;
use Mamba\Command\ControllerDeleteCommand;
use Mamba\Tests\MambaTest;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;

class ControllerCreateAndDeleteCommandTest extends MambaTest
{
    public function testExecute()
    {
        $this->registerCommand(new ControllerCreateCommand($this->app));
        $commandTester = new CommandTester($this->command);

        /** @var QuestionHelper $helper */
        $helper = $this->command->getHelper('question');

        // 1. Create Controller
        $helper->setInputStream($this->getInputStream('Acme'));
        $commandTester->execute([

        ]);
        $output = $commandTester->getDisplay();
        $this->assertContains('Controller Acme was successfully created.', $output);

        // 2. Says Controller exists
        $helper->setInputStream($this->getInputStream('Acme'));
        $commandTester->execute([

        ]);
        $output = $commandTester->getDisplay();
        $this->assertContains('File src/Controller/AcmeController.php already exists.', $output);

        // 3. Delete Controller
        $this->registerCommand(new ControllerDeleteCommand($this->app));
        $commandTester = new CommandTester($this->command);

        /** @var QuestionHelper $helper */
        $helper = $this->command->getHelper('question');
        $helper->setInputStream($this->getInputStream('AcmeController'));
        $commandTester->execute([

        ]);
        $output = $commandTester->getDisplay();
        $this->assertContains('Controller AcmeController was successfully deleted.', $output);
    }


}
