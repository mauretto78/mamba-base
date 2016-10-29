<?php

namespace Mamba\Command\Tests;

use Mamba\Command\ApiCreateCommand;
use Mamba\Command\EntityCreateCommand;
use Mamba\Tests\MambaTest;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;

class ApiCreatorCommandTest extends MambaTest
{
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

        // 2. Create Api on Entity
        $this->setCommand(new ApiCreateCommand($this->app));
        $commandTester = new CommandTester($this->command);

        /** @var QuestionHelper $helper */
        $helper = $this->command->getHelper('question');
        $helper->setInputStream($this->getInputStream(
            "Acme\n"
            ."2.0\n"
        ));
        $commandTester->execute([]);
        echo $output = $commandTester->getDisplay();
    }
}
