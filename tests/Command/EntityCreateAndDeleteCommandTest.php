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
        // 1. Create Entity
        $this->setCommand(new EntityCreateCommand($this->app));
        $commandTester = new CommandTester($this->command);

        /** @var QuestionHelper $helper */
        $helper = $this->command->getHelper('question');
        $helper->setInputStream($this->getInputStream(
            "Acme\n"
            . "acme\n"
            . "title:string(length='100', nullable=true, unique=false)|body:text|ranking:decimal(precision=10, scale=0)\n"
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
            . "acme\n"
            . "title:string(length='100', nullable=true, unique=false)|body:text|ranking:decimal(precision=10, scale=0)\n"
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
