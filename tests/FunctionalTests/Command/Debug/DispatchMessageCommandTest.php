<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Command\Debug;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class DispatchMessageCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $application = new Application(self::bootKernel());

        $command = $application->find('eventoj:debug:dispatch-message');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Messages dispatched!', $output);
    }
}
