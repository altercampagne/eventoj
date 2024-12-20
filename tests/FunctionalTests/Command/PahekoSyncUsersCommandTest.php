<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Command;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class PahekoSyncUsersCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $application = new Application(self::bootKernel());
        UserFactory::new()->many(10)->create();

        $command = $application->find('eventoj:paheko:sync:users');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString("All users have been sync'ed on Paheko!", $output);
    }

    public function testExecuteWithEmail(): void
    {
        $application = new Application(self::bootKernel());

        $command = $application->find('eventoj:paheko:sync:users');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['email' => UserFactory::createOne()->getEmail()]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString("User have been sync'ed on Paheko!", $output);
    }
}
