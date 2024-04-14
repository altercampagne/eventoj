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
        yield ['/event/altertour-2023', 'AlterTour 2023', 'AlterTour 2023'];
        yield ['/event/altertour-2023/de-herisson-a-tortezais', 'AlterTour 2023 - De Hérisson à Tortezais', 'De Hérisson à Tortezais'];
        yield ['/alternative/les-champs-de-lile', 'Les Champs de l\'Île', 'Les Champs de l\'Île'];
    }

    /**
     * @return iterable<array{string, string}>
     */
    public static function adminPages(): iterable
    {
        $eventSlug = 'at-a-venir-ouvert';

        yield ['/_admin/', 'Dashboard'];
        yield ['/_admin/users', 'Toustes les utilisateurices'];
        yield ['/_admin/registrations', 'Toutes les réservations'];
        yield ['/_admin/events', 'Tous les évènements'];
        yield ["/_admin/events/$eventSlug", 'AT à venir (ouvert)'];
        yield ["/_admin/events/$eventSlug/registrations", 'AT à venir (ouvert)'];
        yield ["/_admin/events/$eventSlug/filling", 'AT à venir (ouvert)'];
        yield ["/_admin/events/$eventSlug/meals", 'AT à venir (ouvert)'];
        yield ["/_admin/events/$eventSlug/arrivals", 'AT à venir (ouvert)'];
    }

    /**
     * @dataProvider publicPages
     */
    public function testPublicPages(string $url, string $expectedTitle, ?string $expectedH1 = null): void
    {
        $this->checkPage(static::createClient(), $url, $expectedTitle, $expectedH1);
    }

    /**
     * @dataProvider adminPages
     */
    public function testAdminPages(string $url, string $expectedTitle): void
    {
        $client = static::createClient();
        $user = $this->getRandomAdminUser();
        $client->loginUser($user);

        $this->checkPage($client, $url, 'Admin - '.$expectedTitle, $expectedTitle);

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
