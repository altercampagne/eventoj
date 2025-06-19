<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Admin;

use App\Factory\EventFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class EventShowTest extends WebTestCase
{
    use Factories;

    public function test(): void
    {
        $client = static::createClient();

        $user = UserFactory::new()->admin()->create();
        $event = EventFactory::createOne();

        $client->loginUser($user);

        $client->request('GET', '/_admin/events/'.$event->getSlug());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', $event->getName());
    }
}
