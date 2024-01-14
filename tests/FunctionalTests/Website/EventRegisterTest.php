<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Website;

use App\Entity\Meal;
use App\Tests\DatabaseUtilTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventRegisterTest extends WebTestCase
{
    use DatabaseUtilTrait;

    public function testHomepage(): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $client = static::createClient([], [
            'HTTPS' => true,
        ]);

        $user = $this->getRandomUser();
        $client->loginUser($user);

        $event = $this->getBookableEvent();
        $client->request('GET', "/event/{$event->getSlug()}/register");

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', $event->getName().': Inscription');

        // $client->submitForm('Régler via Helloasso', [
        // 'event_registration_form[stageStart]' => $event->getStages()->get(0)->getId(),
        // 'event_registration_form[firstMeal]' => $faker->randomElement(Meal::class)->value,
        // 'event_registration_form[stageEnd]' => $event->getStages()->get(6)->getId(),
        // 'event_registration_form[lastMeal]' => $faker->randomElement(Meal::class)->value,
        // 'event_registration_form[needBike]' => $faker->boolean(),
        // 'event_registration_form[pricePerDay]' => $faker->numberBetween(25, 45),
        // ]);

        // $this->assertResponseRedirects();
        // $client->followRedirect();
        // $this->assertResponseIsSuccessful();
    }
}
