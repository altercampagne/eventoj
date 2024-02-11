<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Website;

use App\DataFixtures\Util\FixtureBuilder;
use App\Entity\Meal;
use App\Tests\DatabaseUtilTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MostSimpleTest extends WebTestCase
{
    use DatabaseUtilTrait;

    public function testRegistration(): void
    {
        $client = static::createClient();
        $faker = \Faker\Factory::create('fr_FR');

        $event = FixtureBuilder::createAT();
        $this->save($event);

        $user = $this->getRandomUser();
        $client->loginUser($user);

        $client->request('GET', "/event/{$event->getSlug()}/register");

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Qui voyage ?');

        $client->submitForm('Étape suivante', [
            'choose_people_form[neededBike]' => 1,
        ]);

        $this->assertResponseRedirects();
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('event_register_choose_dates');
        $this->assertSelectorTextContains('h2', 'Choix des dates');

        $client->submitForm('Étape suivante', [
            /* @phpstan-ignore-next-line */
            'choose_dates_form[stageStart]' => $event->getStages()->get(0)->getId(),
            /* @phpstan-ignore-next-line */
            'choose_dates_form[firstMeal]' => $faker->randomElement(Meal::class)->value,
            /* @phpstan-ignore-next-line */
            'choose_dates_form[stageEnd]' => $event->getStages()->get(20)->getId(),
            /* @phpstan-ignore-next-line */
            'choose_dates_form[lastMeal]' => $faker->randomElement(Meal::class)->value,
        ]);

        $this->assertResponseRedirects();
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('event_register_choose_price');
    }
}
