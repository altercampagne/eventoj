<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Website;

use App\Factory\EventFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class RegisterTest extends WebTestCase
{
    use Factories;

    public function testRegisterStartingFromEventPage(): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $client = static::createClient();

        $event = EventFactory::new()->published()->withRandomStages()->create();
        $client->request('GET', "/event/{$event->getSlug()}/register");

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('event_registration_need_account');

        $client->clickLink("S'inscrire");
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('register');

        $this->assertSelectorTextContains('h1', 'Inscription');

        $registrationEmail = $faker->email();

        $client->submitForm("Je m'inscris", [
            'registration_form[firstName]' => $faker->firstName(),
            'registration_form[lastName]' => $faker->lastName(),
            'registration_form[birthDate]' => (new \DateTimeImmutable('-20 years'))->format('Y-m-d'),
            'registration_form[email]' => $registrationEmail,
            'registration_form[phoneNumber]' => '0606060606',
            'registration_form[address][countryCode]' => 'FR',
            'registration_form[address][addressLine1]' => $faker->address(),
            'registration_form[address][zipCode]' => $faker->postCode(),
            'registration_form[address][city]' => $faker->city(),
            'registration_form[plainPassword]' => 'password',
        ]);

        $this->assertQueuedEmailCount(1);

        /* @phpstan-ignore-next-line */
        $email = $this->getMailerEvent()->getMessage();
        $this->assertEmailHasHeader($email, 'subject', 'Merci de confirmer ton adresse mail.');
        $this->assertEmailAddressContains($email, 'to', $registrationEmail);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('profile_update_profile');
        $this->assertSelectorExists('#connected-as');
        $this->assertSelectorTextContains('h1', 'Création de ton profil');

        $client->submitForm('Mettre à jour', [
            'profile_update_form[diet]' => 'vegetarian',
            'profile_update_form[glutenIntolerant]' => false,
            'profile_update_form[lactoseIntolerant]' => false,
            'profile_update_form[dietDetails]' => null,
            'profile_update_form[biography]' => "I'm only an humble test user.",
        ]);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('event_register');
    }
}
