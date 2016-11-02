<?php

namespace Mamba\Command\Tests;

use Mamba\Command\ApiCreateCommand;
use Mamba\Command\ApiDeleteCommand;
use Mamba\Command\EntityCreateCommand;
use Mamba\Tests\MambaTest;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;

class ApiCreateAndDeleteCommandTest extends MambaTest
{
    public function testDeletingNotExistingEntity()
    {
        $this->setCommand(new ApiDeleteCommand($this->app));
        $commandTester = new CommandTester($this->command);

        /** @var QuestionHelper $helper */
        $helper = $this->command->getHelper('question');

        // 1. Not Existing Entity
        $helper->setInputStream($this->getInputStream('NotExistingEntity'));
        $commandTester->execute([

        ]);
        $output = $commandTester->getDisplay();
        $this->assertContains('API based on NotExistingEntity Entity does not exists.', $output);
    }

    public function testExecute()
    {
        // 1. Create Entity
        $this->setCommand(new EntityCreateCommand($this->app));
        $commandTester = new CommandTester($this->command);

        /** @var QuestionHelper $helper */
        $helper = $this->command->getHelper('question');
        $helper->setInputStream($this->getInputStream(
            "Api\n"
            ."api\n"
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
        $this->assertContains('Entity Api was successfully created.', $output);

        // 2. Create Api on Entity
        $this->setCommand(new ApiCreateCommand($this->app));
        $commandTester = new CommandTester($this->command);

        /** @var QuestionHelper $helper */
        $helper = $this->command->getHelper('question');
        $helper->setInputStream($this->getInputStream(
            "Api\n"
            ."2.0\n"
        ));
        $commandTester->execute([]);
        $output = $commandTester->getDisplay();
        $this->assertContains('API on Entity Api was successfully deleted.', $output);
        
        // 3. Delete Api, Entity, Repository and Controller
        $this->setCommand(new ApiDeleteCommand($this->app));
        $commandTester = new CommandTester($this->command);

        /** @var QuestionHelper $helper */
        $helper = $this->command->getHelper('question');
        $helper->setInputStream($this->getInputStream('Api'));
        $commandTester->execute([]);
        $output = $commandTester->getDisplay();
        $this->assertContains('API based on Api Entity was successfully deleted.', $output);
    }
}
