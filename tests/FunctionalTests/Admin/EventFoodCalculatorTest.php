<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Admin;

use App\Factory\EventFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class EventFoodCalculatorTest extends WebTestCase
{
    use Factories;

    public function testWithPublishedEvent(): void
    {
        $client = static::createClient();

        $user = UserFactory::new()->admin()->create();
        $event = EventFactory::new()->published()->create();

        $client->loginUser($user);

        $client->request('GET', "/_admin/events/{$event->getSlug()}/food-calculator");

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', "{$event->getName()}: calculateur de nourriture");
    }

    public function testWithNotPublishedEvent(): void
    {
        $client = static::createClient();

        $user = UserFactory::new()->admin()->create();
        $event = EventFactory::createOne();

        $client->loginUser($user);

        $client->request('GET', "/_admin/events/{$event->getSlug()}/food-calculator");

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('admin_event_show');
        $this->assertSelectorTextContains('.alert-warning', "Le calculateur de nourriture n'est disponible qu'une fois l'évènement publié.");
    }
}
