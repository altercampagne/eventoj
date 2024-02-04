<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Website;

use App\Tests\DatabaseUtilTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterTest extends WebTestCase
{
    use DatabaseUtilTrait;

    public function testRegisterStartingFromEventPage(): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $client = static::createClient();

        $event = $this->getBookableEvent();
        $client->request('GET', "/event/{$event->getSlug()}/register");

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('login');

        $client->clickLink('Créer un compte');
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('register');

        $this->assertSelectorTextContains('h1', 'Inscription');

        $registrationEmail = $faker->email();

        $client->submitForm('Je m\'inscris', [
            'registration_form[firstName]' => $faker->firstName(),
            'registration_form[lastName]' => $faker->lastName(),
            'registration_form[birthDate]' => $faker->date(),
            'registration_form[email]' => $registrationEmail,
            'registration_form[phoneNumber]' => $faker->phoneNumber(),
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
        $this->assertSelectorTextContains('.alert-success', "📢 Ton compte a été créé : tu peux dès maintenant t'inscrire aux évènements de d'Altercampagne !");
        $this->assertSelectorExists('#connected-as');

        $client->submitForm('Mettre à jour', [
            'profile_update_form[diet]' => 'vegetarian',
            'profile_update_form[glutenIntolerant]' => false,
            'profile_update_form[lactoseIntolerant]' => false,
            'profile_update_form[dietDetails]' => null,
            'profile_update_form[biography]' => 'I\'m only an humble test user.',
        ]);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('event_register');
    }
}
