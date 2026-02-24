<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Admin\RegistrationCreate;

// use App\Tests\FunctionalTests\WebTestCase;
use App\Entity\Meal;
use App\Factory\CompanionFactory;
use App\Factory\EventFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class NominalCaseTest extends WebTestCase
{
    use Factories;

    public function test(): void
    {
        $client = static::createClient();

        $event = EventFactory::new()->AT()->withRandomStages()->published()->create();
        \assert(null !== $event->getFirstStage());
        \assert(null !== $event->getLastStage());
        $user = UserFactory::new()->create();
        $companion1 = CompanionFactory::new()->children()->create(['user' => $user]);
        $companion2 = CompanionFactory::new()->adult()->create(['user' => $user]);

        $client->loginUser(UserFactory::new()->admin()->create());

        $crawler = $client->request('GET', "/_admin/events/{$event->getSlug()}/registration_create");
        $this->assertResponseIsSuccessful();

        // As there's an autocomplete field, we cannot use
        // `$client->submitForm`: there's no available option in select. That's
        // why we directly make an HTTP request.
        $client->request('POST', "/_admin/events/{$event->getSlug()}/registration_create", [
            'choose_user_form' => [
                'user' => $user->getId(),
                '_token' => $crawler->filter('input[name="choose_user_form[_token]"]')->attr('value'),
            ],
        ]);

        $this->assertResponseRedirects("/_admin/events/{$event->getSlug()}/registration_create/{$user->getId()}");
        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        $client->submitForm("Créer l'inscription", [
            'details_form[companions]' => [$companion1->getId(), $companion2->getId()],
            'details_form[bikes]' => 2,
            'details_form[start]' => $event->getFirstStage()->getDate()->format('Y-m-d'),
            'details_form[firstMeal]' => Meal::BREAKFAST->value,
            'details_form[end]' => $event->getLastStage()->getDate()->format('Y-m-d'),
            'details_form[lastMeal]' => Meal::DINNER->value,
            'details_form[price]' => 142,
        ]);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('.alert-success', "L'inscription de {$user->getPublicName()} pour l'évènement {$event->getName()} a été créée avec succès !");
    }
}
