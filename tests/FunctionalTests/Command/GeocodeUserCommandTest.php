<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Command;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class GeocodeUserCommandTest extends KernelTestCase
{
    public function testExecuteWithSlug(): void
    {
        $application = new Application(self::bootKernel());

        $command = $application->find('eventoj:geocode:users');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['email' => UserFactory::createOne()->getEmail()], ['--force']);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('[OK] User address have been successfully geocoded.', $output);
    }
}
