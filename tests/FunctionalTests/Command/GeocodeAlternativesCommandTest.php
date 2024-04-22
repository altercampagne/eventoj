<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class GeocodeAlternativesCommandTest extends KernelTestCase
{
    public function testExecuteWithSlug(): void
    {
        $application = new Application(self::bootKernel());

        $command = $application->find('eventoj:geocode:alternatives');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['slug' => 'solaire-2000'], ['--force']);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('[OK] Alternative address have been successfully geocoded.', $output);
    }
}
