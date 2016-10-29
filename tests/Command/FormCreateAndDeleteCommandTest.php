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
        // 1. Create Entity
        $this->setCommand(new FormCreateCommand($this->app));
        $commandTester = new CommandTester($this->command);

        /** @var QuestionHelper $helper */
        $helper = $this->command->getHelper('question');
        $helper->setInputStream($this->getInputStream(
            "Acme\n"
            ."first name\n"
            ."0\n"
            ."y\n"
            ."email\n"
            ."1\n"
            ."y\n"
            ."select\n"
            ."2\n"
            ."y\n"
            ."number\n"
            ."3\n"
            ."y\n"
            ."note\n"
            ."4\n"
            ."n\n"
        ));
        $commandTester->execute([]);
        $output = $commandTester->getDisplay();
        $this->assertContains('Form AcmeType was successfully created.', $output);

        // 2. Says Form exists
        $this->setCommand(new FormCreateCommand($this->app));
        $commandTester = new CommandTester($this->command);

        /** @var QuestionHelper $helper */
        $helper = $this->command->getHelper('question');
        $helper->setInputStream($this->getInputStream(
            "Acme\n"
            ."first name\n"
            ."0\n"
            ."y\n"
            ."email\n"
            ."1\n"
            ."y\n"
            ."select\n"
            ."2\n"
            ."y\n"
            ."number\n"
            ."3\n"
            ."y\n"
            ."note\n"
            ."4\n"
            ."n\n"
        ));
        $commandTester->execute([]);
        $output = $commandTester->getDisplay();
        $this->assertContains('File src/Type/AcmeType.php already exists.', $output);

        // 3. Delete Form
        $this->setCommand(new FormDeleteCommand($this->app));
        $commandTester = new CommandTester($this->command);

        /** @var QuestionHelper $helper */
        $helper = $this->command->getHelper('question');
        $helper->setInputStream($this->getInputStream('AcmeType'));
        $commandTester->execute([]);
        $output = $commandTester->getDisplay();
        $this->assertContains('Form AcmeType was successfully deleted.', $output);
    }
}
