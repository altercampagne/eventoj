<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests;

use App\Tests\DatabaseUtilTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BasicGetTest extends WebTestCase
{
    use DatabaseUtilTrait;

    /**
     * @return iterable<array{string, string, ?string}>
     */
    public static function publicPages(): iterable
    {
        yield ['/', 'Évènements proposés par AlterCampagne', 'Tous les évènements'];
    }

    /**
     * @return iterable<array{string, string, ?string}>
     */
    public static function protectedPages(): iterable
    {
        yield ['/_admin', 'Admin - Dashboard', 'Dashboard'];
        yield ['/_admin/users', 'Admin - Utilisateurices', 'Toustes les utilisateurices'];
        yield ['/_admin/registrations', 'Admin - Réservations', 'Toutes les réservations'];
        yield ['/_admin/events', 'Admin - Évènements', 'Tous les évènements'];
    }

    /**
     * @dataProvider publicPages
     */
    public function testPublicPages(string $url, string $expectedTitle, ?string $expectedH1 = null): void
    {
        $this->checkPage(static::createClient(), $url, $expectedTitle, $expectedH1);
    }

    /**
     * @dataProvider protectedPages
     */
    public function testProtectedPages(string $url, string $expectedTitle, ?string $expectedH1 = null): void
    {
        $client = static::createClient();
        $user = $this->getRandomAdminUser();
        $client->loginUser($user);

        $this->checkPage($client, $url, $expectedTitle, $expectedH1);

        $this->assertSelectorExists('#connected-as');
    }

    private function checkPage(KernelBrowser $client, string $url, string $expectedTitle, ?string $expectedH1 = null): void
    {
        $client->request('GET', $url);

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains($expectedTitle);
        if (null !== $expectedH1) {
            $this->assertSelectorTextContains('h1', $expectedH1);
        }
    }
}
