<?php

namespace Mamba\Command\Tests;

use Mamba\Command\RouterDebugCommand;
use Mamba\Tests\MambaTest;
use Symfony\Component\Console\Tester\CommandTester;

class RouterDebugCommandTest extends MambaTest
{
    public function testExecute()
    {
        $this->setCommand(new RouterDebugCommand($this->app));
        $commandTester = new CommandTester($this->command);
        $commandTester->execute(array(

        ));

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertContains('dummy-url', $output);
        $this->assertContains('multiple-method-url', $output);
        $this->assertContains('dummy-url-with-no-method', $output);
        $this->assertContains('dummy-url-with-default-values', $output);
        $this->assertContains('dummy-url-with-requirements', $output);
    }
}
