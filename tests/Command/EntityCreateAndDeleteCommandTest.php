<?php

namespace Mamba\Command\Tests;

use Mamba\Command\EntityCreateCommand;
use Mamba\Command\EntityDeleteCommand;
use Mamba\Tests\MambaTest;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;

class EntityCreateAndDeleteCommandTest extends MambaTest
{
    public function testExecute()
    {
        $this->setCommand(new EntityCreateCommand($this->app));
        $commandTester = new CommandTester($this->command);

        /** @var QuestionHelper $helper */
        $helper = $this->command->getHelper('question');

        // 1. Create Entity
        $helper->setInputStream($this->getInputStream(
            "Acme\n"
            . "Acme\n"
            . "title:string(length=100 nullable=true unique=false) body:text ranking:decimal(precision=10 scale=0)\n"
        ));
        $commandTester->execute([

        ]);
        $output = $commandTester->getDisplay();
        $this->assertContains('Entity Acme was successfully created.', $output);
    }
}
