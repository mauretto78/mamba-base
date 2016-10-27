<?php

namespace Mamba\Command\Tests;

use Mamba\Command\FormCreateCommand;
use Mamba\Command\FormDeleteCommand;
use Mamba\Tests\MambaTest;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;

class FormCreateAndDeleteCommandTest extends MambaTest
{

    public function testDeletingNotWritableForm()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testDeletingNotExistingForm()
    {
        $this->setCommand(new FormDeleteCommand($this->app));
        $commandTester = new CommandTester($this->command);

        /** @var QuestionHelper $helper */
        $helper = $this->command->getHelper('question');

        // Try to delete a not existing Form
        $helper->setInputStream($this->getInputStream('NotExistingForm'));
        $commandTester->execute([

        ]);
        $output = $commandTester->getDisplay();
        $this->assertContains('NotExistingForm does not exists.', $output);
    }

    public function testExecute()
    {

    }
}
