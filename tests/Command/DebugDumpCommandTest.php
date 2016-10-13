<?php

namespace Mamba\Command\Tests;

use Mamba\Command\DebugDumpCommand;
use Mamba\Tests\MambaTest;
use Symfony\Component\Console\Tester\CommandTester;

class DebugDumpCommandTest extends MambaTest
{
    public function testExecute()
    {
        $this->registerCommand(new DebugDumpCommand($this->app));
        $commandTester = new CommandTester($this->command);
        $commandTester->execute(array(

        ));

        // the output of the command in the console
        $output = $commandTester->getDisplay();

        $this->assertContains('argument_metadata_factory', $output);
        $this->assertContains('argument_resolver', $output);
        $this->assertContains('argument_value_resolvers', $output);
        $this->assertContains('callback_resolver', $output);
        $this->assertContains('charset', $output);
        $this->assertContains('console', $output);
        $this->assertContains('console.name', $output);
        $this->assertContains('console.project_directory', $output);
        $this->assertContains('console.version', $output);
        $this->assertContains('controllers', $output);
        $this->assertContains('controllers_factory', $output);
        $this->assertContains('debug', $output);
        $this->assertContains('dispatcher', $output);
        $this->assertContains('exception_handler', $output);
        $this->assertContains('kernel', $output);
        $this->assertContains('logger', $output);
        $this->assertContains('request.http_port', $output);
        $this->assertContains('request.https_port', $output);
        $this->assertContains('request_context', $output);
        $this->assertContains('request_matcher', $output);
        $this->assertContains('request_stack', $output);
        $this->assertContains('resolver', $output);
        $this->assertContains('route_class', $output);
        $this->assertContains('route_factory', $output);
        $this->assertContains('routes', $output);
        $this->assertContains('routes_factory', $output);
        $this->assertContains('url_generator', $output);
    }
}
