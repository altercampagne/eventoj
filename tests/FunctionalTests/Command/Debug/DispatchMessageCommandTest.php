<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Command\Debug;

use App\Factory\PaymentFactory;
use App\Factory\RegistrationFactory;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Zenstruck\Foundry\Test\Factories;

class DispatchMessageCommandTest extends KernelTestCase
{
    use Factories;

    public function testExecute(): void
    {
        $application = new Application(self::bootKernel());
        PaymentFactory::new()->approved()->create(['registration' => RegistrationFactory::new()->confirmed()->create()]);

        $command = $application->find('eventoj:debug:dispatch-message');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Messages dispatched!', $output);
    }
}
