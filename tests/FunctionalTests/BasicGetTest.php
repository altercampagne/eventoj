<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests;

use App\Factory\UserFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class BasicGetTest extends WebTestCase
{
    use Factories;

    /**
     * @return iterable<array{0: string, 1?: string, 2?: string}>
     */
    public static function publicPages(): iterable
    {
        yield ['/', "Bienvenue sur la partie membres du site d'AlterCampagne", 'Bienvenue à toi !'];
        yield ['/event', 'Tous les évènements proposés par AlterCampagne', 'Tous les évènements'];
        yield ['/event/altertour-2023', 'AlterTour 2023', 'AlterTour 2023'];
        yield ['/event/altertour-2023/de-herisson-a-tortezais', 'AlterTour 2023: De Hérisson à Tortezais le 12 juillet 2023', 'De Hérisson à Tortezais'];
        yield ['/alternative/les-champs-de-l-ile', 'Les Champs de l\'Île', 'Les Champs de l\'Île'];
        yield ['/contact', "Contacter l'association", 'Nous contacter'];
        yield ['/faq', 'Foire aux questions', 'Foire aux questions'];
        yield ['/robots.txt'];
    }

    /**
     * @return iterable<array{0: string, 1?: string, 2?: string}>
     */
    public static function memberPages(): iterable
    {
        yield ['/pictures', 'Toutes les photos de nos évènements', 'Les photos de nos évènements'];
        yield ['/pictures/altertour-2023', 'AlterTour 2023: Toutes les photos', 'AlterTour 2023: Les photos'];
        yield ['/pictures/altertour-2023/upload', 'AlterTour 2023: Envoi de tes photos', 'AlterTour 2023: Tes photos'];
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
        yield ["/_admin/events/{$eventSlug}", 'AT à venir (ouvert)'];
        yield ["/_admin/events/{$eventSlug}/registrations", 'AT à venir (ouvert)'];
        yield ["/_admin/events/{$eventSlug}/filling", 'AT à venir (ouvert)'];
        yield ["/_admin/events/{$eventSlug}/meals", 'AT à venir (ouvert)'];
        yield ["/_admin/events/{$eventSlug}/arrivals", 'AT à venir (ouvert)'];
    }

    #[DataProvider('publicPages')]
    public function testPublicPages(string $url, ?string $expectedTitle = null, ?string $expectedH1 = null): void
    {
        $this->checkPage(static::createClient(), $url, $expectedTitle, $expectedH1);
    }

    #[DataProvider('memberPages')]
    public function testMemberPages(string $url, string $expectedTitle, ?string $expectedH1 = null): void
    {
        $client = static::createClient();

        // Ensure page is protected
        $client->request('GET', $url);
        $this->assertResponseStatusCodeSame(302);

        // Check with a user
        $user = UserFactory::new()->create();
        $client->loginUser($user);

        $this->checkPage($client, $url, $expectedTitle, $expectedH1);

        $this->assertSelectorExists('#connected-as');
    }

    #[DataProvider('adminPages')]
    public function testAdminPages(string $url, string $expectedTitle): void
    {
        $client = static::createClient();
        $user = UserFactory::new()->admin()->create();
        $client->loginUser($user);

        $this->checkPage($client, $url, 'Admin - '.$expectedTitle, $expectedTitle);

        $this->assertSelectorExists('#connected-as');
    }

    private function checkPage(KernelBrowser $client, string $url, ?string $expectedTitle, ?string $expectedH1 = null): void
    {
        $client->request('GET', $url);

        $this->assertResponseIsSuccessful();
        if (null !== $expectedTitle) {
            $this->assertPageTitleContains($expectedTitle);
        }

        if (null !== $expectedH1) {
            $this->assertSelectorTextContains('h1', $expectedH1);
        }
    }
}
