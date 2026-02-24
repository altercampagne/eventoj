<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Admin\RegistrationCreate;

use App\Factory\EventFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class EventNotPublishedTest extends WebTestCase
{
    use Factories;

    public function testCreateForNotPublishedEventIsNotPossible(): void
    {
        $client = static::createClient();

        $event = EventFactory::new()->AT()->withRandomStages()->create();

        $client->loginUser(UserFactory::new()->admin()->create());

        $client->request('GET', "/_admin/events/{$event->getSlug()}/registration_create");

        $this->assertResponseRedirects("/_admin/events/{$event->getSlug()}");
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.alert-error', 'Impossible de créer une inscription pour un évènement non publié.');
    }
}
