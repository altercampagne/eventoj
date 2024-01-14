<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Website;

use App\Tests\DatabaseUtilTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfileUpdateTest extends WebTestCase
{
    use DatabaseUtilTrait;

    public function testWithoutUpdatingEmail(): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $client = static::createClient();

        $user = $this->getRandomUser();

        $client->loginUser($user);

        $client->request('GET', '/me/profile');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Mise à jour de ton profil');

        $client->submitForm('Mettre à jour', [
            'profile_update_form[firstName]' => 'First',
            'profile_update_form[lastName]' => 'Last',
            'profile_update_form[birthDate]' => $faker->date(),
            'profile_update_form[email]' => $user->getEmail(),
            'profile_update_form[phoneNumber]' => $faker->phoneNumber(),
            'profile_update_form[address][countryCode]' => 'FR',
            'profile_update_form[address][addressLine1]' => $faker->address(),
            'profile_update_form[address][zipCode]' => $faker->postCode(),
            'profile_update_form[address][city]' => $faker->city(),
        ]);

        // Email has not been changed so no email have been queued
        $this->assertQueuedEmailCount(0);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('profile_update');
        $this->assertSelectorTextContains('.alert-success', 'Ton profil a bien été mis à jour !');
        $this->assertSelectorTextContains('#connected-as', 'First Last');
    }

    public function testWhenUpdatingEmail(): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $client = static::createClient();
        $client->loginUser($this->getRandomUser());

        $client->request('GET', '/me/profile');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Mise à jour de ton profil');

        $newEmail = $faker->email();

        $client->submitForm('Mettre à jour', [
            'profile_update_form[firstName]' => $faker->firstName(),
            'profile_update_form[lastName]' => $faker->lastName(),
            'profile_update_form[birthDate]' => $faker->date(),
            'profile_update_form[email]' => $newEmail,
            'profile_update_form[phoneNumber]' => $faker->phoneNumber(),
            'profile_update_form[address][countryCode]' => 'FR',
            'profile_update_form[address][addressLine1]' => $faker->address(),
            'profile_update_form[address][zipCode]' => $faker->postCode(),
            'profile_update_form[address][city]' => $faker->city(),
        ]);

        $this->assertQueuedEmailCount(1);

        /* @phpstan-ignore-next-line */
        $email = $this->getMailerEvent()->getMessage();
        $this->assertEmailHasHeader($email, 'subject', 'Merci de confirmer ton adresse mail.');
        $this->assertEmailAddressContains($email, 'to', $newEmail);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('profile_update');
        $this->assertSelectorTextContains('.alert-info', 'Ton adresse mail a été modifiée, merci de la valider grâce au mail qui vient de t\'être envoyé.');
    }
}
