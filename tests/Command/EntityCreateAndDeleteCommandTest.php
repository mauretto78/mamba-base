<?php

namespace Mamba\Command\Tests;

use Mamba\Command\EntityCreateCommand;
use Mamba\Command\EntityDeleteCommand;
use Mamba\Tests\MambaTest;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;

class EntityCreateAndDeleteCommandTest extends MambaTest
{
    public function testDeletingNotWritableEntity()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testDeletingNotExistingEntity()
    {
        $this->setCommand(new EntityDeleteCommand($this->app));
        $commandTester = new CommandTester($this->command);

        /** @var QuestionHelper $helper */
        $helper = $this->command->getHelper('question');

        // 1. Create Controller
        $helper->setInputStream($this->getInputStream('NotExistingCommand'));
        $commandTester->execute([

        ]);
        $output = $commandTester->getDisplay();
        $this->assertContains('NotExistingCommand does not exists.', $output);
    }

    public function testExecute()
    {
        // 1. Create Entity
        $this->setCommand(new EntityCreateCommand($this->app));
        $commandTester = new CommandTester($this->command);

        /** @var QuestionHelper $helper */
        $helper = $this->command->getHelper('question');
        $helper->setInputStream($this->getInputStream(
            "Acme\n"
            ."acme\n"
            ."first name\n"
            ."0\n"
            ."0\n"
            ."y\n"
            ."is integer\n"
            ."1\n"
            ."1\n"
            ."y\n"
            ."is boolean\n"
            ."4\n"
            ."1\n"
            ."y\n"
            ."text\n"
            ."10\n"
            ."1\n"
            ."n\n"
        ));
        $commandTester->execute([]);
        $output = $commandTester->getDisplay();
        $this->assertContains('Entity Acme was successfully created.', $output);

        // 2. Says Entity exists
        $this->setCommand(new EntityCreateCommand($this->app));
        $commandTester = new CommandTester($this->command);

        /** @var QuestionHelper $helper */
        $helper = $this->command->getHelper('question');
        $helper->setInputStream($this->getInputStream(
            "Acme\n"
            ."acme\n"
            ."first name\n"
            ."0\n"
            ."0\n"
            ."y\n"
            ."a type of int\n"
            ."1\n"
            ."1\n"
            ."y\n"
            ."a type of bool\n"
            ."4\n"
            ."1\n"
            ."y\n"
            ."text\n"
            ."10\n"
            ."1\n"
            ."n\n"
        ));
        $commandTester->execute([]);
        $output = $commandTester->getDisplay();
        $this->assertContains('File src/Entity/Acme.php already exists.', $output);

        // 3. Delete Entity
        $this->setCommand(new EntityDeleteCommand($this->app));
        $commandTester = new CommandTester($this->command);

        /** @var QuestionHelper $helper */
        $helper = $this->command->getHelper('question');
        $helper->setInputStream($this->getInputStream('Acme'));
        $commandTester->execute([]);
        $output = $commandTester->getDisplay();
        $this->assertContains('Entity Acme was successfully deleted.', $output);
    }
}
